<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Entity\AuthenticationInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Change a user's username
 */
class ChangeUsernameCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:change-username')
            ->setDescription('Change the username for a user.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            );
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        $allUsernames = array_map(
            fn(string $username) => strtolower($username),
            $this->authenticationRepository->getUsernames()
        );

        $question = new Question("New Username: ");
        $question->setValidator(function ($answer) use ($allUsernames) {
            if (in_array(strtolower($answer), $allUsernames)) {
                throw new \RuntimeException(
                    "Username already in use"
                );
            }

            return $answer;
        });
        $username = $this->getHelper('question')->ask($input, $output, $question);

        $authentication = $user->getAuthentication();
        if (!$authentication) {
            /** @var AuthenticationInterface $authentication */
            $authentication = $this->authenticationRepository->create();
            $user->setAuthentication($authentication);
        }

        $authentication->setUsername($username);
        $this->authenticationRepository->update($authentication);

        $output->writeln('<info>Username Changed.</info>');

        return 0;
    }
}
