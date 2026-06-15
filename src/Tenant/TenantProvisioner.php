<?php

namespace App\Tenant;

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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Provisions a tenant end to end: creates its database, builds the schema by
 * running the ERP migrations from empty (migrations are the single source of
 * truth), seeds the minimum data (TVA, timbre, société, admin user), and
 * records the tenant + owner + trial subscription in the control plane.
 *
 * Used by both the tenant:provision CLI command and (later) the self-service
 * signup flow. Rolls back (drops the database, removes control rows) on any
 * failure and throws {@see ProvisioningException} with a user-safe message.
 */
class TenantProvisioner
{
    public const RESERVED_SUBDOMAINS = ['www', 'app', 'api', 'admin', 'mail', 'smtp', 'ftp', 'moudir', 'static', 'assets', 'cdn'];
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
    }

    public function isSubdomainAvailable(string $subdomain): bool
    {
        $subdomain = strtolower(trim($subdomain));

        return $this->isValidSubdomain($subdomain)
            && $this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain]) === null;
    }

    /**
     * @throws ProvisioningException
     */
    public function provision(string $subdomain, string $companyName, string $ownerEmail, string $plainPassword, string $planCode = 'starter'): Tenant
    {
        $subdomain = strtolower(trim($subdomain));
        $ownerEmail = strtolower(trim($ownerEmail));

        if (!$this->isValidSubdomain($subdomain)) {
            throw new ProvisioningException('Sous-domaine invalide : 3 à 63 caractères (lettres, chiffres, tirets).');
        }
        if (\in_array($subdomain, self::RESERVED_SUBDOMAINS, true)) {
            throw new ProvisioningException(sprintf('Le sous-domaine « %s » est réservé.', $subdomain));
        }
        if (!filter_var($ownerEmail, \FILTER_VALIDATE_EMAIL)) {
            throw new ProvisioningException('Adresse e-mail invalide.');
        }
        if (\strlen($plainPassword) < 6) {
            throw new ProvisioningException('Le mot de passe doit contenir au moins 6 caractères.');
        }
        if ($this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain])) {
            throw new ProvisioningException(sprintf('Le sous-domaine « %s » est déjà pris.', $subdomain));
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
                ->setCompanyName($companyName)
                ->setDbName($dbName)
                ->setStatus(Tenant::STATUS_TRIAL);
            $this->controlEm->persist($tenant);
            $this->controlEm->flush();

            [$code, $migrateOutput] = $this->migrator->migrate($dbName);
            if ($code !== 0) {
                throw new ProvisioningException("Échec des migrations du locataire :\n" . $migrateOutput);
            }

            $this->tenantConnection->selectDatabase($dbName);
            $this->seedTenant($companyName, $ownerEmail, $plainPassword);

            $this->createControlRecords($tenant, $ownerEmail, $plainPassword, $planCode);

            return $tenant;
        } catch (\Throwable $e) {
            $this->rollback($tenant, $dbCreated ? $dbName : null);

            throw $e instanceof ProvisioningException
                ? $e
                : new ProvisioningException('Échec du provisionnement : ' . $e->getMessage(), 0, $e);
        }
    }

    private function isValidSubdomain(string $subdomain): bool
    {
        return \strlen($subdomain) >= 3
            && (bool) preg_match('/^[a-z0-9]([a-z0-9-]{1,61}[a-z0-9])?$/', $subdomain);
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
