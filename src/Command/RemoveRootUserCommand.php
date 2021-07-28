<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Revokes root-level privileges from a given user.
 *
 * Class RemoveRootUserCommand
 */
class RemoveRootUserCommand extends Command
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'ilios:remove-root-user';

    public function __construct(protected UserRepository $userRepository)
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setAliases(['ilios:maintenance:remove-root-user'])
            ->setDescription('Revokes root-level privileges from a given user.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                "The user's id."
            );
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /* @var UserInterface $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception("No user with id #{$userId} was found.");
        }
        $user->setRoot(false);
        $this->userRepository->update($user, true, true);
        $output->writeln("Root-level privileges have been revoked from user with id #{$userId}.");

        return 0;
    }
}
