<?php

namespace App\Command;

use App\Entity\UserInterface;
use App\Entity\AuthenticationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\UserManager;

/**
 * Change a users's username
 */
class ChangeUsernameCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AuthenticationManager
     */
    protected $authenticationManager;

    public function __construct(
        UserManager $userManager,
        AuthenticationManager $authenticationManager
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
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
        $user = $this->userManager->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        $allUsernames = array_map(function (string $username) {
            return strtolower($username);
        }, $this->authenticationManager->getUsernames());

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
            $authentication = $this->authenticationManager->create();
            $user->setAuthentication($authentication);
        }

        $authentication->setUsername($username);
        $this->authenticationManager->update($authentication);

        $output->writeln('<info>Username Changed.</info>');
    }
}
