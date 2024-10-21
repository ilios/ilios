<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    protected function configure(): void
    {
        $this->addArgument('userId', InputArgument::REQUIRED, "The user's id.");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('userId');
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
