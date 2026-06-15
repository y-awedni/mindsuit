<?php

namespace App\Command;

use App\Tenant\ProvisioningException;
use App\Tenant\TenantProvisioner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Thin CLI wrapper over {@see TenantProvisioner}. The signup flow reuses the
 * same service.
 */
#[AsCommand(name: 'tenant:provision', description: 'Create and seed a new tenant')]
class TenantProvisionCommand extends Command
{
    public function __construct(private readonly TenantProvisioner $provisioner)
    {
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

        $subdomain = (string) $input->getArgument('subdomain');
        $company = $input->getOption('company') ?: ucfirst(strtolower(trim($subdomain)));
        $email = (string) ($input->getOption('owner-email') ?: 'admin@' . strtolower(trim($subdomain)) . '.local');
        $password = (string) ($input->getOption('owner-password') ?: bin2hex(random_bytes(6)));

        try {
            $tenant = $this->provisioner->provision($subdomain, $company, $email, $password, (string) $input->getOption('plan'));
        } catch (ProvisioningException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf(
            "Tenant \"%s\" provisioned.\n  URL:      https://%s.<APP_BASE_DOMAIN>\n  Login:    %s\n  Password: %s\n  Database: %s",
            $tenant->getSubdomain(), $tenant->getSubdomain(), $email, $password, $tenant->getDbName()
        ));

        return Command::SUCCESS;
    }
}
