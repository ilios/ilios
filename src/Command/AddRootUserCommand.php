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
        $user->setRoot(true);
        $this->userRepository->update($user, true, true);
        $output->writeln("User with id #{$userId} has been granted root-level privileges.");

        return Command::SUCCESS;
    }
}
