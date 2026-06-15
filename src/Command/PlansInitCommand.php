<?php

namespace App\Command;

use App\Entity\Control\Plan;
use App\Subscription\PlanLimit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Seeds / updates the default subscription plans (idempotent, keyed by code).
 * Prices are in millimes (1 DT = 1000). These are starting defaults — the
 * operator edits them from the dashboard afterwards.
 */
#[AsCommand(name: 'plans:init', description: 'Create or update the default subscription plans')]
class PlansInitCommand extends Command
{
    private const DEFAULTS = [
        [
            'code' => 'starter', 'name' => 'Starter', 'priceMonthly' => 0, 'priceYearly' => null,
            'sortOrder' => 1, 'limits' => [PlanLimit::DOCS_PER_MONTH => 10, PlanLimit::USERS => 1],
        ],
        [
            'code' => 'pro', 'name' => 'Pro', 'priceMonthly' => 39000, 'priceYearly' => 374400,
            'sortOrder' => 2, 'limits' => [PlanLimit::DOCS_PER_MONTH => 200, PlanLimit::USERS => 5],
        ],
        [
            'code' => 'entreprise', 'name' => 'Entreprise', 'priceMonthly' => 99000, 'priceYearly' => 950400,
            'sortOrder' => 3, 'limits' => [PlanLimit::DOCS_PER_MONTH => null, PlanLimit::USERS => null],
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $controlEm)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->controlEm->getRepository(Plan::class);

        foreach (self::DEFAULTS as $def) {
            $plan = $repo->findOneBy(['code' => $def['code']]) ?? new Plan();
            $existed = $plan->getId() !== null;

            $plan->setCode($def['code'])
                ->setName($def['name'])
                ->setPriceMonthly($def['priceMonthly'])
                ->setPriceYearly($def['priceYearly'])
                ->setSortOrder($def['sortOrder'])
                ->setActive(true);

            // Only set limits on first creation; don't clobber operator edits.
            if (!$existed) {
                $plan->setLimits($def['limits']);
            }

            $this->controlEm->persist($plan);
            $io->writeln(sprintf('%s plan "%s"', $existed ? 'Updated' : 'Created', $def['code']));
        }

        $this->controlEm->flush();
        $io->success('Plans synced.');

        return Command::SUCCESS;
    }
}
