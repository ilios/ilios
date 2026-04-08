<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

/**
 * Invalidate all user tokens issued before now
 *
 * Class InvalidateUserTokenCommand
 */
#[AsCommand(
    name: 'ilios:invalidate-user-tokens',
    description: 'Invalidate all user tokens issued before now.',
    aliases: ['ilios:maintenance:invalidate-user-tokens']
)]
class InvalidateUserTokenCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'A valid user id.', name: 'userId')] string $userId,
    ): int {
        $now = new DateTime();
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception(
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

        return Command::SUCCESS;
    }
}
