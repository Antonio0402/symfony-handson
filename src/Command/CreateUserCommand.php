<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user',
)]
class CreateUserCommand {
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher, 
        private EntityManagerInterface $entity
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('User\'s email')] string $email,
        #[Argument('User\'s password')] string $password,
    ): int {
        $io->note(sprintf('The value of $email is: %s', var_export($email, true)));
        $io->note(sprintf('The value of $password is: %s', var_export($password, true)));

        $user = new User();
        $user->setEmail($email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $this->entity->persist($user);
        $this->entity->flush();
        $io->success(sprintf('User %s account was created', $email));

        return Command::SUCCESS;
    }
}
