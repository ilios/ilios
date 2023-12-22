<?php

declare(strict_types=1);

namespace App\Command;

use App\Classes\SessionUser;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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

    protected function configure(): void
    {
        $this
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            )
            ->addOption(
                'ttl',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the interval before the token expires?',
                'PT8H'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('userId');
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception(
                "No user with id #{$userId} was found."
            );
        }
        $jwt = $this->jwtManager->createJwtFromUserId($user->getId(), $input->getOption('ttl'));

        $output->writeln('Success!');
        $output->writeln('Token ' . $jwt);

        return Command::SUCCESS;
    }
}
