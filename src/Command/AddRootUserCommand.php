<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Grants root-level privileges to a given user.
 *
 * Class AddRootUserCommand
 */
#[AsCommand(
    name: 'ilios:add-root-user',
    description: 'Grants root-level privileges to a given user.',
    aliases: ['ilios:maintenance:add-root-user']
)]
class AddRootUserCommand extends Command
{
    public function __construct(protected UserRepository $userRepository)
    {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: "The user's id.", name: 'userId')] string $userId,
    ): int {
        /** @var ?UserInterface $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception("No user with id #{$userId} was found.");
        }
        $user->setRoot(true);
        $this->userRepository->update($user, true, true);
        $output->writeln("User with id #{$userId} has been granted root-level privileges.");

        return Command::SUCCESS;
    }
}
