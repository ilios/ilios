<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Add a user by looking them up in the directory
 *
 * Class AddUserCommand
 */
#[AsCommand(
    name: 'ilios:add-user',
    description: 'Add a user to ilios.',
    aliases: ['ilios:maintenance:add-user']
)]
class AddUserCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository,
        protected SchoolRepository $schoolRepository,
        protected UserPasswordHasherInterface $hasher,
        protected SessionUserProvider $sessionUserProvider
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $userOptions = [
            'schoolId',
            'firstName',
            'lastName',
            'email',
            'telephoneNumber',
            'campusId',
            'username',
            'password',
        ];

        foreach ($userOptions as $option) {
            $this->addOption(
                $option,
                null,
                InputOption::VALUE_OPTIONAL,
                "{$option} for new user"
            );
        }

        $this->addOption(
            'isRoot',
            null,
            InputOption::VALUE_OPTIONAL,
            'Grants root privileges to new user.'
        );
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schoolId = $input->getOption('schoolId');
        if (!$schoolId) {
            $schoolTitles = [];
            foreach ($this->schoolRepository->findBy([], ['title' => 'ASC']) as $school) {
                $schoolTitles[$school->getTitle()] = $school->getId();
            }
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                "What is this user's primary school?",
                array_keys($schoolTitles)
            );
            $question->setErrorMessage('School %s is invalid.');

            $schoolTitle = $helper->ask($input, $output, $question);
            $schoolId = $schoolTitles[$schoolTitle];
        }
        $school = $this->schoolRepository->findOneBy(['id' => $schoolId]);
        if (!$school) {
            throw new Exception(
                "School with id {$schoolId} could not be found."
            );
        }
        $userRecord = [
            'firstName'         => $input->getOption('firstName'),
            'lastName'          => $input->getOption('lastName'),
            'email'             => $input->getOption('email'),
            'telephoneNumber'   => $input->getOption('telephoneNumber'),
            'campusId'          => $input->getOption('campusId'),
            'username'          => $input->getOption('username'),
            'password'          => $input->getOption('password'),
            'isRoot'            => (null !== $input->getOption('isRoot')) ? filter_var(
                $input->getOption('isRoot'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) : null,
        ];

        $userRecord = $this->fillUserRecord($userRecord, $input, $output);

        $user = $this->userRepository->findOneBy(['campusId' => $userRecord['campusId']]);
        if ($user) {
            throw new Exception(
                'User #' . $user->getId() . " with campus id {$userRecord['campusId']} already exists."
            );
        }
        $user = $this->userRepository->findOneBy(['email' => $userRecord['email']]);
        if ($user) {
            throw new Exception(
                'User #' . $user->getId() . " with email address {$userRecord['email']} already exists."
            );
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Campus ID', 'First', 'Last', 'Email', 'Username', 'Phone Number', 'Is Root?'])
            ->setRows([
                [
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['email'],
                    $userRecord['username'],
                    $userRecord['telephoneNumber'],
                    $userRecord['isRoot'] ? 'yes' : 'no',
                ],
            ])
        ;
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            "<question>Do you wish to add this user to Ilios in {$school->getTitle()}?</question>\n",
            true
        );

        if ($helper->ask($input, $output, $question)) {
            $user = $this->userRepository->create();
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setEmail($userRecord['email']);
            $user->setCampusId($userRecord['campusId']);
            $user->setAddedViaIlios(true);
            $user->setEnabled(true);
            $user->setSchool($school);
            $user->setUserSyncIgnore(false);
            $user->setRoot($userRecord['isRoot']);
            $this->userRepository->update($user);

            /** @var AuthenticationInterface $authentication */
            $authentication = $this->authenticationRepository->create();
            $authentication->setUsername($userRecord['username']);

            $user->setAuthentication($authentication);
            $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);

            $hashedPassword = $this->hasher->hashPassword($sessionUser, $userRecord['password']);
            $authentication->setPasswordHash($hashedPassword);

            $this->authenticationRepository->update($authentication);

            $output->writeln(
                '<info>Success! New user #' . $user->getId() . ' ' . $user->getFirstAndLastName() . ' created.</info>'
            );
            return Command::SUCCESS;
        } else {
            $output->writeln('<comment>Canceled.</comment>');

            return Command::FAILURE;
        }
    }

    protected function fillUserRecord(array $userRecord, InputInterface $input, OutputInterface $output): array
    {
        if (empty($userRecord['firstName'])) {
            $userRecord['firstName'] = $this->askForString('First Name', 1, 50, $input, $output);
        }
        if (empty($userRecord['lastName'])) {
            $userRecord['lastName'] = $this->askForString('Last Name', 1, 50, $input, $output);
        }
        if (empty($userRecord['telephoneNumber'])) {
            $userRecord['telephoneNumber'] = $this->askForString('Phone Number', 0, 30, $input, $output);
        }
        if (empty($userRecord['campusId'])) {
            $userRecord['campusId'] = $this->askForString('Campus ID', 0, 16, $input, $output);
        }
        if (empty($userRecord['username'])) {
            $userRecord['username'] = $this->askForString('Username', 1, 100, $input, $output);
        }
        if (empty($userRecord['password'])) {
            $question = new Question("What is the user's password? ");
            $question->setValidator(function ($answer) {
                if (strlen($answer) < 7) {
                    throw new RuntimeException(
                        "Password must be at least 7 character"
                    );
                }

                return $answer;
            });
            $question->setHidden(true);
            $userRecord['password'] = $this->getHelper('question')->ask($input, $output, $question);
        }
        if (empty($userRecord['email'])) {
            $question = new Question("What is the user's Email Address? ");
            $question->setValidator(function ($answer) {
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException(
                        "Email is not valid"
                    );
                }
                return $answer;
            });
            $userRecord['email'] = $this->getHelper('question')->ask($input, $output, $question);
        }

        if (null === $userRecord['isRoot']) {
            $question = new ConfirmationQuestion("Grant root privileges to new user?", false);
            $userRecord['isRoot'] = $this->getHelper('question')->ask($input, $output, $question);
        }


        return $userRecord;
    }

    protected function askForString(
        string $what,
        int $min,
        int $max,
        InputInterface $input,
        OutputInterface $output,
    ): mixed {
        $question = new Question("What is the user's {$what}? ");
        $question->setValidator(function ($answer) use ($what, $min, $max) {
            if (strlen($answer) < $min) {
                throw new RuntimeException(
                    "{$what} must be at least {$min} character"
                );
            }
            if (strlen($answer) >= $max) {
                throw new RuntimeException(
                    "{$what} must be less than {$max} characters"
                );
            }
            return $answer;
        });
        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
