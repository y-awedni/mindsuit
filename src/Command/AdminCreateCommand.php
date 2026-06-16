<?php

namespace App\Command;

use App\Entity\Control\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'admin:create', description: 'Create a super-admin operator account')]
class AdminCreateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $existing = $this->controlEm->getRepository(AdminUser::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $io->error(sprintf('Admin "%s" already exists.', $email));

            return Command::FAILURE;
        }

        $admin = new AdminUser();
        $admin->setEmail($email);
        $admin->setRoles(['ROLE_SUPER_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, $password));

        $this->controlEm->persist($admin);
        $this->controlEm->flush();

        $io->success(sprintf('Admin "%s" created.', $email));

        return Command::SUCCESS;
    }
}
