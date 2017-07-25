<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\ManagerInterface;

use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\UserRoleManager;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Creates a first user account with Course Director privileges.
 *
 * Class InstallFirstUserCommand
 */
class InstallFirstUserCommand extends Command
{
    /**
     * @var string
     */
    const USERNAME = 'first_user';

    /**
     * @var string
     */
    const PASSWORD = 'Ch4nge_m3';

    /**
     * @var string
     */
    const FIRST_NAME = 'First';

    /**
     * @var string
     */
    const LAST_NAME = 'User';

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @var UserRoleManager
     */
    protected $userRoleManager;

    /**
     * @var  AuthenticationManager
     */
    protected $authenticationManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * Constructor.
     * @param UserManager $userManager
     * @param SchoolManager $schoolManager
     * @param UserRoleManager $userRoleManager
     * @param AuthenticationManager $authenticationManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserManager $userManager,
        SchoolManager $schoolManager,
        UserRoleManager $userRoleManager,
        AuthenticationManager $authenticationManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userManager = $userManager;
        $this->schoolManager = $schoolManager;
        $this->userRoleManager = $userRoleManager;
        $this->authenticationManager = $authenticationManager;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:setup:first-user')
            ->setDescription('Creates a first user account with "Course Director" privileges.')
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // prevent this command to run on a non-empty user store.
        $existingUser = $this->userManager->findOneBy([]);
        if (! empty($existingUser)) {
            throw new \Exception(
                'Sorry, at least one user record already exists. Cannot create a "first" user account.'
            );
        }

        $schools = $this->schoolManager->findBy([], ['title' => 'ASC']);

        // check if any school data is present before invoking the form helper
        // to prevent the form from breaking on missing school data further downstream.
        if (empty($schools)) {
            throw new \Exception('No schools found. Please load schools into this Ilios instance first.');
        }

        $schoolId = $input->getOption('school');
        if (!$schoolId) {
            $schoolTitles = [];
            /* @var SchoolInterface $school */
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
        $school = $this->schoolManager->findOneBy(['id' => $schoolId]);
        if (!$school) {
            throw new \Exception(
                "School with id {$schoolId} could not be found."
            );
        }

        $email = $input->getOption('email');
        if (! $email) {
            $question = new Question("What is the user's Email Address? ");
            $question->setValidator(function ($answer) {
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException(
                        "Email is not valid"
                    );
                }
                return $answer;
            });
            $email = $this->getHelper('question')->ask($input, $output, $question);
        }

        $user = $this->userManager->create();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($email);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);

        $user->addRole($this->userRoleManager->findOneBy(['title' => 'Developer']));
        $user->addRole($this->userRoleManager->findOneBy(['title' => 'Course Director']));
        $user->setSchool($school);
        $this->userManager->update($user);

        /** @var AuthenticationInterface $authentication */
        $authentication = $this->authenticationManager->create();

        $authentication->setUser($user);
        $user->setAuthentication($authentication);
        $sessionUser = $authentication->getSessionUser();

        $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, self::PASSWORD);

        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordBcrypt($encodedPassword);
        $this->authenticationManager->update($authentication);

        $output->writeln('Success!');
        $output->writeln('A user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'.", self::USERNAME, self::PASSWORD));
        $output->writeln('Please change this password as soon as possible.');
    }
}
