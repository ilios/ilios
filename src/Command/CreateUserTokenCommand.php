<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\JsonWebTokenManager;

/**
 * Create a new token for a user
 *
 * Class CreateUserTokenCommand
 */
#[AsCommand(
    name: 'ilios:create-user-token',
    description: 'Create a new API token for a user.',
    aliases: ['ilios:maintenance:create-user-token']
)]
class CreateUserTokenCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected JsonWebTokenManager $jwtManager
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'A valid user id.', name: 'userId')] int $userId,
        #[Option(description: 'What is the interval before the token expires?')] string $ttl = 'PT8H'
    ): int {
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception(
                "No user with id #{$userId} was found."
            );
        }
        $jwt = $this->jwtManager->createJwtFromUserId($user->getId(), $ttl);

        $output->writeln('Success!');
        $output->writeln('Token ' . $jwt);

        return Command::SUCCESS;
    }
}
