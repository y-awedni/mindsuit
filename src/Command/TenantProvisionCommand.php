<?php

namespace App\Command;

use App\Doctrine\TenantConnection;
use App\Doctrine\TenantMigrator;
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
 * migrations from empty (migrations are the single source of truth for the
 * schema), seeds the minimum data (TVA, timbre, société, admin user), and
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
        private readonly TenantMigrator $migrator,
        private readonly UserPasswordHasherInterface $passwordHasher,
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

        if ($this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain])) {
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

            $io->writeln('Running ERP migrations...');
            [$code, $migrateOutput] = $this->migrator->migrate($dbName);
            if ($code !== 0) {
                throw new \RuntimeException("Tenant migrations failed:\n" . $migrateOutput);
            }

            $io->writeln('Seeding tenant data...');
            $this->tenantConnection->selectDatabase($dbName);
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

    private function seedTenant(string $company, string $email, string $password): void
    {
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
