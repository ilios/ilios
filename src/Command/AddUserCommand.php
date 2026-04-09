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
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(description: 'School ID for new user', name: 'schoolId')] ?int $schoolId = null,
        #[Option(description: 'First name for new user', name: 'firstName')] ?string $firstName = null,
        #[Option(description: 'Last name for new user', name: 'lastName')] ?string $lastName = null,
        #[Option(description: 'Email for new user')] ?string $email = null,
        #[Option(
            description: 'Telephone number for new user',
            name: 'telephoneNumber'
        )] ?string $telephoneNumber = null,
        #[Option(description: 'Campus ID for new user', name: 'campusId')] ?string $campusId = null,
        #[Option(description: 'Username for new user')] ?string $username = null,
        #[Option(description: 'Password for new user')] ?string $password = null,
        #[Option(description: 'Grants root privileges to the new user.', name: 'isRoot')] ?string $isRoot = null,
    ): int {
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
           'firstName'         => $firstName,
           'lastName'          => $lastName,
           'email'             => $email,
           'telephoneNumber'   => $telephoneNumber,
           'campusId'          => $campusId,
           'username'          => $username,
           'password'          => $password,
           'isRoot'            => (null !== $isRoot ? filter_var(
               $isRoot,
               FILTER_VALIDATE_BOOLEAN,
               FILTER_NULL_ON_FAILURE
           ) : null),
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
