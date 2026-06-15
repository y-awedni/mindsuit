<?php

namespace App\Command;

use App\Doctrine\TenantConnection;
use App\Doctrine\TenantContext;
use App\Entity\Control\Tenant;
use App\Subscription\LimitExceededException;
use App\Subscription\PlanLimit;
use App\Subscription\PlanLimitChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Diagnostic: report plan-limit usage for a given tenant (mirrors the runtime
 * resolution path: TenantContext set + tenant connection switched).
 */
#[AsCommand(name: 'tenant:limits', description: 'Report plan-limit usage for a tenant')]
class LimitProbeCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        #[Autowire(service: 'doctrine.dbal.tenant_connection')]
        private readonly TenantConnection $tenantConnection,
        private readonly TenantContext $tenantContext,
        private readonly PlanLimitChecker $checker,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('subdomain', InputArgument::REQUIRED, 'Tenant subdomain');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subdomain = (string) $input->getArgument('subdomain');

        $tenant = $this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain]);
        if (!$tenant) {
            $io->error(sprintf('No tenant "%s".', $subdomain));

            return Command::FAILURE;
        }

        $this->tenantContext->setTenant($tenant);
        $this->tenantConnection->selectDatabase($tenant->getDbName());

        $rows = [];
        foreach (PlanLimit::labels() as $key => $label) {
            ['cap' => $cap, 'used' => $used] = $this->checker->usage($key);
            try {
                $this->checker->assertCanAdd($key);
                $verdict = 'OK';
            } catch (LimitExceededException) {
                $verdict = 'BLOCKED';
            }
            $rows[] = [$label, $key, $used, $cap === null ? '∞' : (string) $cap, $verdict];
        }

        $io->section(sprintf('%s (plan: %s)', $tenant->getSubdomain(), $tenant->getPlan()?->getCode() ?? 'none'));
        $io->table(['Limit', 'Key', 'Used', 'Cap', 'Add 1?'], $rows);

        return Command::SUCCESS;
    }
}
