<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Revokes root-level privileges from a given user.
 *
 * Class RemoveRootUserCommand
 */
#[AsCommand(
    name: 'ilios:remove-root-user',
    description: 'Revokes root-level privileges from a given user.',
    aliases: ['ilios:maintenance:remove-root-user'],
)]
class RemoveRootUserCommand extends Command
{
    public function __construct(protected UserRepository $userRepository)
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: "The user's id.", name: 'userId')] string $userId,
    ): int {
        /** @var ?UserInterface $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception("No user with id #{$userId} was found.");
        }
        $user->setRoot(false);
        $this->userRepository->update($user, true, true);
        $output->writeln("Root-level privileges have been revoked from user with id #{$userId}.");

        return Command::SUCCESS;
    }
}
