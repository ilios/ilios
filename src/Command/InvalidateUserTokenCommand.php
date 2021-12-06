<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

/**
 * Invalidate all user tokens issued before now
 *
 * Class InvalidateUserTokenCommand
 */
class InvalidateUserTokenCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:invalidate-user-tokens')
            ->setAliases(['ilios:maintenance:invalidate-user-tokens'])
            ->setDescription('Invalidate all user tokens issued before now.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new DateTime();
        $userId = $input->getArgument('userId');
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }

        $authentication = $user->getAuthentication();
        if (!$authentication) {
            $authentication = $this->authenticationRepository->create();
            $authentication->setUser($user);
        }

        $authentication->setInvalidateTokenIssuedBefore($now);
        $this->authenticationRepository->update($authentication);

        $output->writeln('Success!');
        $output->writeln(
            'All the tokens for ' . $user->getFirstAndLastName() .
            ' issued before Today at ' . $now->format('g:i:s A e') .
            ' have been invalidated.'
        );

        return 0;
    }
}
