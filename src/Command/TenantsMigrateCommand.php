<?php

namespace App\Command;

use App\Doctrine\TenantMigrator;
use App\Entity\Control\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Runs the per-tenant ERP migrations across every active/trial tenant database
 * (or a single one via --tenant). Wired into the deploy workflow after the
 * control-plane migration.
 */
#[AsCommand(name: 'tenants:migrate', description: 'Run ERP migrations on all tenant databases')]
class TenantsMigrateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        private readonly TenantMigrator $migrator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('tenant', null, InputOption::VALUE_REQUIRED, 'Migrate only this tenant (subdomain)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $criteria = ['status' => [Tenant::STATUS_TRIAL, Tenant::STATUS_ACTIVE]];
        if ($only = $input->getOption('tenant')) {
            $criteria = ['subdomain' => $only];
        }

        $tenants = $this->controlEm->getRepository(Tenant::class)->findBy($criteria);
        if (!$tenants) {
            $io->warning('No matching tenants to migrate.');

            return Command::SUCCESS;
        }

        $failed = 0;
        foreach ($tenants as $tenant) {
            $io->section(sprintf('%s (%s)', $tenant->getSubdomain(), $tenant->getDbName()));
            [$code, $out] = $this->migrator->migrate($tenant->getDbName());
            $output->writeln($out);
            if ($code !== 0) {
                $failed++;
                $io->error(sprintf('Migration failed for %s (exit %d).', $tenant->getSubdomain(), $code));
            }
        }

        if ($failed > 0) {
            $io->error(sprintf('%d tenant(s) failed to migrate.', $failed));

            return Command::FAILURE;
        }

        $io->success(sprintf('Migrated %d tenant(s).', \count($tenants)));

        return Command::SUCCESS;
    }
}
