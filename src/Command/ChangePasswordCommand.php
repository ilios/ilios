<?php

namespace App\Command;

use App\Entity\UserInterface;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\UserManager;

/**
 * Change a users's password
 */
class ChangePasswordCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AuthenticationManager
     */
    protected $authenticationManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    public function __construct(
        UserManager $userManager,
        AuthenticationManager $authenticationManager,
        UserPasswordEncoderInterface $encoder,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->encoder = $encoder;
        $this->sessionUserProvider = $sessionUserProvider;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:change-password')
            ->setDescription('Change the password for a user.')
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

        $question = new Question("Password: ");
        $question->setValidator(function ($answer) {
            if (strlen($answer) < 7) {
                throw new \RuntimeException(
                    "Password must be at least 7 character"
                );
            }

            return $answer;
        });
        $question->setHidden(true);
        $password = $this->getHelper('question')->ask($input, $output, $question);

        $authentication = $user->getAuthentication();
        if (!$authentication) {
            /** @var AuthenticationInterface $authentication */
            $authentication = $this->authenticationManager->create();
            $user->setAuthentication($authentication);
        }

        $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);

        $encodedPassword = $this->encoder->encodePassword($sessionUser, $password);
        $authentication->setPasswordBcrypt($encodedPassword);

        $this->authenticationManager->update($authentication);

        $output->writeln('<info>Password Changed.</info>');
    }
}
