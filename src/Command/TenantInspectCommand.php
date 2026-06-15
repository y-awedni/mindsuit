<?php

namespace App\Command;

use App\Doctrine\TenantConnection;
use App\Entity\Article;
use App\Entity\Control\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Diagnostic: resolve a tenant by subdomain, switch the tenant connection to
 * its database, and report what the tenant entity manager sees. Useful to
 * verify tenant isolation from the CLI.
 */
#[AsCommand(name: 'tenant:inspect', description: 'Switch to a tenant DB and report what the tenant EM sees')]
class TenantInspectCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        private readonly EntityManagerInterface $tenantEm,
        #[Autowire(service: 'doctrine.dbal.tenant_connection')]
        private readonly TenantConnection $tenantConnection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('subdomain', InputArgument::REQUIRED, 'Tenant subdomain to inspect');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subdomain = $input->getArgument('subdomain');

        $tenant = $this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain]);
        if (!$tenant) {
            $io->error(sprintf('No tenant with subdomain "%s" in the control DB.', $subdomain));

            return Command::FAILURE;
        }

        $this->tenantConnection->selectDatabase($tenant->getDbName());

        $activeDb = $this->tenantConnection->fetchOne('SELECT DATABASE()');
        $articleCount = $this->tenantEm->getRepository(Article::class)->count([]);

        $io->section(sprintf('Tenant "%s"', $subdomain));
        $io->table(['Property', 'Value'], [
            ['Company', $tenant->getCompanyName()],
            ['Configured dbName', $tenant->getDbName()],
            ['Active DB (SELECT DATABASE())', $activeDb],
            ['Articles visible to tenant EM', (string) $articleCount],
        ]);

        return Command::SUCCESS;
    }
}
