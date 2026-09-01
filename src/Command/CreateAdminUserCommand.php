<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates a new admin user interactively.',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // SymfonyStyle provides a beautiful interactive interface
        $io = new SymfonyStyle($input, $output);
        $io->title('User Creation Wizard');

        // 1. Collect user input
        $email = $io->ask('Enter the user email');

        // askHidden ensures the password isn't visible on the screen while typing
        $password = $io->askHidden('Enter the password');

        $passwordConfirm = $io->askHidden('Confirm');

        if ($password !== $passwordConfirm) {
            $io->error('Passwords do not match!');
            return Command::FAILURE;
        }

        // 2. Create the User entity
        $user = new User();
        $user->setEmail($email);

        // Hash the password before saving
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Optional: Set default roles
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        // 3. Save to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 4. Output success message
        $io->success(sprintf('User %s was successfully created!', $email));

        return Command::SUCCESS;
    }
}
