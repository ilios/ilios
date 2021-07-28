<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Change a users's password
 */
class ChangePasswordCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository,
        protected UserPasswordEncoderInterface $encoder,
        protected SessionUserProvider $sessionUserProvider
    ) {
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new Exception(
                "No user with id #{$userId} was found."
            );
        }

        $question = new Question("Password: ");
        $question->setValidator(function ($answer) {
            if (strlen($answer) < 7) {
                throw new RuntimeException(
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
            $authentication = $this->authenticationRepository->create();
            $user->setAuthentication($authentication);
        }

        $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);

        $encodedPassword = $this->encoder->encodePassword($sessionUser, $password);
        $authentication->setPasswordHash($encodedPassword);

        $this->authenticationRepository->update($authentication);

        $output->writeln('<info>Password Changed.</info>');

        return 0;
    }
}
