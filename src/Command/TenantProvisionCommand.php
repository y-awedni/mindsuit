<?php

namespace App\Command;

use App\Doctrine\TenantConnection;
use App\Entity\Control\Owner;
use App\Entity\Control\Plan;
use App\Entity\Control\Subscription;
use App\Entity\Control\Tenant;
use App\Entity\Societe;
use App\Entity\Timbre;
use App\Entity\Tva;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Provisions a new tenant end to end: creates its database, runs the ERP
 * migrations, seeds the minimum data (TVA, timbre, société, admin user), and
 * records the tenant + owner + trial subscription in the control plane.
 *
 * Rolls back (drops the database, removes control rows) on any failure.
 */
#[AsCommand(name: 'tenant:provision', description: 'Create and seed a new tenant')]
class TenantProvisionCommand extends Command
{
    private const RESERVED = ['www', 'app', 'api', 'admin', 'mail', 'smtp', 'ftp', 'moudir', 'static', 'assets', 'cdn'];
    private const TRIAL_DAYS = 14;

    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        private readonly EntityManagerInterface $tenantEm,
        #[Autowire(service: 'doctrine.dbal.tenant_connection')]
        private readonly TenantConnection $tenantConnection,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('subdomain', InputArgument::REQUIRED, 'Subdomain, e.g. "acme" for acme.moudir.pro')
            ->addOption('company', null, InputOption::VALUE_REQUIRED, 'Company display name')
            ->addOption('owner-email', null, InputOption::VALUE_REQUIRED, 'Owner / admin email')
            ->addOption('owner-password', null, InputOption::VALUE_REQUIRED, 'Owner / admin password')
            ->addOption('plan', null, InputOption::VALUE_REQUIRED, 'Plan code', 'starter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $subdomain = strtolower(trim((string) $input->getArgument('subdomain')));
        $company = $input->getOption('company') ?: ucfirst($subdomain);
        $email = strtolower((string) ($input->getOption('owner-email') ?: 'admin@' . $subdomain . '.local'));
        $password = (string) ($input->getOption('owner-password') ?: bin2hex(random_bytes(6)));

        if (!preg_match('/^[a-z0-9]([a-z0-9-]{1,61}[a-z0-9])?$/', $subdomain) || \strlen($subdomain) < 3) {
            $io->error('Invalid subdomain. Use 3-63 chars: letters, digits, hyphens.');

            return Command::FAILURE;
        }
        if (\in_array($subdomain, self::RESERVED, true)) {
            $io->error(sprintf('Subdomain "%s" is reserved.', $subdomain));

            return Command::FAILURE;
        }

        $controlRepo = $this->controlEm->getRepository(Tenant::class);
        if ($controlRepo->findOneBy(['subdomain' => $subdomain])) {
            $io->error(sprintf('A tenant with subdomain "%s" already exists.', $subdomain));

            return Command::FAILURE;
        }

        $dbName = 'tenant_' . str_replace('-', '_', $subdomain);
        $controlConn = $this->controlEm->getConnection();
        $dbCreated = false;
        $tenant = null;

        try {
            $controlConn->executeStatement(sprintf('CREATE DATABASE `%s` CHARACTER SET utf8 COLLATE utf8_general_ci', $dbName));
            $dbCreated = true;

            $tenant = (new Tenant())
                ->setSubdomain($subdomain)
                ->setCompanyName($company)
                ->setDbName($dbName)
                ->setStatus(Tenant::STATUS_TRIAL);
            $this->controlEm->persist($tenant);
            $this->controlEm->flush();

            $io->writeln('Building ERP schema...');
            $this->tenantConnection->selectDatabase($dbName);
            $this->createTenantSchema();
            // Record the current migrations as applied so future incremental
            // migrations run on top of this freshly-built schema.
            $this->recordMigrationsAsApplied();

            $io->writeln('Seeding tenant data...');
            $this->seedTenant($company, $email, $password);

            $this->createControlRecords($tenant, $email, $password, (string) $input->getOption('plan'));

            $io->success(sprintf(
                "Tenant \"%s\" provisioned.\n  URL:      https://%s.<APP_BASE_DOMAIN>\n  Login:    %s\n  Password: %s\n  Database: %s",
                $subdomain, $subdomain, $email, $password, $dbName
            ));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Provisioning failed: ' . $e->getMessage());
            $this->rollback($tenant, $dbCreated ? $dbName : null);

            return Command::FAILURE;
        }
    }

    /**
     * Loads the canonical ERP schema (a structure-only dump of the reference
     * tenant) into the freshly-created tenant database. Using the proven dump
     * avoids SchemaTool's FK-ordering issues with the legacy schema; the dump
     * toggles FOREIGN_KEY_CHECKS itself. The connection is already pointed at
     * the new tenant database.
     */
    private function createTenantSchema(): void
    {
        $path = $this->projectDir . '/migrations/tenant_schema.sql';
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Schema template not found: %s', $path));
        }

        foreach ($this->splitSqlStatements($sql) as $statement) {
            $this->tenantConnection->executeStatement($statement);
        }
    }

    /**
     * @return list<string>
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        foreach (preg_split('/;\s*\n/', $sql) as $chunk) {
            $lines = array_filter(
                explode("\n", $chunk),
                static fn (string $line): bool => !str_starts_with(ltrim($line), '--')
            );
            $statement = trim(implode("\n", $lines));
            if ($statement !== '') {
                $statements[] = $statement;
            }
        }

        return $statements;
    }

    /**
     * Records every existing tenant migration as already applied (the schema
     * was just built from the ORM mappings), so later incremental migrations
     * apply on top. Done with direct SQL to avoid running migration commands
     * in-process (which freezes the shared Doctrine migrations factory).
     */
    private function recordMigrationsAsApplied(): void
    {
        $conn = $this->tenantConnection;
        $conn->executeStatement(
            'CREATE TABLE IF NOT EXISTS doctrine_migration_versions '
            . '(version VARCHAR(191) NOT NULL, executed_at DATETIME DEFAULT NULL, '
            . 'execution_time INT DEFAULT NULL, PRIMARY KEY(version)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );

        foreach (glob($this->projectDir . '/migrations/tenant/Version*.php') as $file) {
            $version = 'DoctrineMigrations\\' . basename($file, '.php');
            $conn->executeStatement(
                'INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, NOW(), 0)',
                [$version]
            );
        }
    }

    private function seedTenant(string $company, string $email, string $password): void
    {
        // Connection is already pointed at the new tenant DB by migrateTenantDatabase().
        $tva = (new Tva())->setTaux('19');

        $timbre = new Timbre();
        $timbre->setValeur('1.000');

        $societe = (new Societe())
            ->setRs($company)
            ->setMf('-')->setRcs('-')->setAdresse('-')->setVille('-')->setPays('Tunisie')
            ->setCodePostale('-')->setTel('-')->setFax('-')->setMobile('-');

        $user = new User();
        $user->setUsername('admin');
        $user->setUsernameCanonical('admin');
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->tenantEm->persist($tva);
        $this->tenantEm->persist($timbre);
        $this->tenantEm->persist($societe);
        $this->tenantEm->persist($user);
        $this->tenantEm->flush();
    }

    private function createControlRecords(Tenant $tenant, string $email, string $password, string $planCode): void
    {
        $plan = $this->controlEm->getRepository(Plan::class)->findOneBy(['code' => $planCode]);
        if ($plan) {
            $tenant->setPlan($plan);
        }

        $owner = (new Owner())
            ->setEmail($email)
            ->setTenant($tenant);
        // Owner is not a security user yet; store a bcrypt hash for later use.
        $owner->setPassword(password_hash($password, \PASSWORD_BCRYPT));
        $this->controlEm->persist($owner);

        if ($plan) {
            $subscription = (new Subscription())
                ->setTenant($tenant)
                ->setPlan($plan)
                ->setStatus(Subscription::STATUS_TRIAL)
                ->setTrialEndsAt(new \DateTimeImmutable(sprintf('+%d days', self::TRIAL_DAYS)));
            $this->controlEm->persist($subscription);
        }

        $this->controlEm->flush();
    }

    private function rollback(?Tenant $tenant, ?string $dbName): void
    {
        if ($dbName !== null) {
            try {
                $this->controlEm->getConnection()->executeStatement(sprintf('DROP DATABASE IF EXISTS `%s`', $dbName));
            } catch (\Throwable) {
            }
        }

        if ($tenant !== null && $tenant->getId() !== null) {
            try {
                $conn = $this->controlEm->getConnection();
                $conn->executeStatement('DELETE FROM payment WHERE subscription_id IN (SELECT id FROM subscription WHERE tenant_id = ?)', [$tenant->getId()]);
                $conn->executeStatement('DELETE FROM subscription WHERE tenant_id = ?', [$tenant->getId()]);
                $conn->executeStatement('DELETE FROM owner WHERE tenant_id = ?', [$tenant->getId()]);
                $conn->executeStatement('DELETE FROM tenant WHERE id = ?', [$tenant->getId()]);
            } catch (\Throwable) {
            }
        }
    }
}
