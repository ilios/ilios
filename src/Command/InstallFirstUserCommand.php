<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Creates a first user account with Course Director privileges.
 *
 * Class InstallFirstUserCommand
 */
#[AsCommand(
    name: 'ilios:setup-first-user',
    description: 'Creates a first user account with root privileges.',
    aliases: ['ilios:setup:first-user']
)]
class InstallFirstUserCommand extends Command
{
    private const string USERNAME = 'first_user';
    private const string PASSWORD = 'Ch4nge_m3';
    private const string FIRST_NAME = 'First';
    private const string LAST_NAME = 'User';

    /**
     * Constructor.
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected SchoolRepository $schoolRepository,
        protected AuthenticationRepository $authenticationRepository,
        protected UserPasswordHasherInterface $passwordHasher,
        protected SessionUserProvider $sessionUserProvider
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'school',
                null,
                InputOption::VALUE_REQUIRED,
                'A valid school id.'
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_REQUIRED,
                'A valid email address.'
            );
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // prevent this command to run on a non-empty user store.
        $existingUser = $this->userRepository->findOneBy([]);
        if (! empty($existingUser)) {
            throw new Exception(
                'Sorry, at least one user record already exists. Cannot create a "first" user account.'
            );
        }

        $schools = $this->schoolRepository->findBy([], ['title' => 'ASC']);

        // check if any school data is present before invoking the form helper
        // to prevent the form from breaking on missing school data further downstream.
        if (empty($schools)) {
            throw new Exception('No schools found. Please load schools into this Ilios instance first.');
        }

        $schoolId = $input->getOption('school');
        if (!$schoolId) {
            $schoolTitles = [];
            /** @var SchoolInterface $school */
            foreach ($schools as $school) {
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

        $email = $input->getOption('email');
        if (! $email) {
            $question = new Question("What is the user's Email Address? ");
            $question->setValidator(function ($answer) {
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException(
                        "Email is not valid"
                    );
                }
                return $answer;
            });
            $email = $this->getHelper('question')->ask($input, $output, $question);
        }

        /** @var UserInterface $user */
        $user = $this->userRepository->create();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($email);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);
        $user->setRoot(true);

        $user->setSchool($school);
        $this->userRepository->update($user);

        /** @var AuthenticationInterface $authentication */
        $authentication = $this->authenticationRepository->create();

        $authentication->setUser($user);
        $user->setAuthentication($authentication);
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);

        $hashedPassword = $this->passwordHasher->hashPassword($sessionUser, self::PASSWORD);

        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordHash($hashedPassword);
        $this->authenticationRepository->update($authentication);

        $output->writeln('Success!');
        $output->writeln('A user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'.", self::USERNAME, self::PASSWORD));
        $output->writeln('Please change this password as soon as possible.');

        return Command::SUCCESS;
    }
}
