<?php

namespace App\Command;

use App\Entity\Control\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Runs the per-tenant ERP migrations across every active/trial tenant database
 * (or a single one via --tenant). Each tenant is migrated in its own
 * subprocess with DATABASE_URL pointed at that tenant's database — a fresh
 * process avoids the shared Doctrine migrations factory freezing between runs.
 *
 * Wired into the deploy workflow after the control-plane migration.
 */
#[AsCommand(name: 'tenants:migrate', description: 'Run ERP migrations on all tenant databases')]
class TenantsMigrateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[Autowire('%env(DATABASE_URL)%')]
        private readonly string $databaseUrl,
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
            [$code, $out] = $this->migrate($tenant->getDbName());
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

    /**
     * @return array{0:int,1:string}
     */
    private function migrate(string $dbName): array
    {
        $url = preg_replace('#(@[^/]+/)[^?]*#', '${1}' . $dbName, $this->databaseUrl, 1);

        $cmd = sprintf(
            'cd %s && DATABASE_URL=%s php bin/console doctrine:migrations:migrate '
            . '--configuration=migrations/tenant.php --em=tenant --no-interaction --allow-no-migration 2>&1',
            escapeshellarg($this->projectDir),
            escapeshellarg($url)
        );

        $lines = [];
        $exit = 0;
        exec($cmd, $lines, $exit);

        return [$exit, implode("\n", $lines)];
    }
}
