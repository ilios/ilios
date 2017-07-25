<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

/**
 * Add a user by looking them up in the directory
 *
 * Class AddUserCommand
 */
class AddUserCommand extends Command
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
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
    
    public function __construct(
        UserManager $userManager,
        AuthenticationManager $authenticationManager,
        SchoolManager $schoolManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->schoolManager = $schoolManager;
        $this->encoder = $encoder;

        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ilios:maintenance:add-user')->setDescription('Add a user to ilios.');
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
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $schoolId = $input->getOption('schoolId');
        if (!$schoolId) {
            $schoolTitles = [];
            foreach ($this->schoolManager->findBy([], ['title' => 'ASC']) as $school) {
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
        $userRecord = [
            'firstName'         => $input->getOption('firstName'),
            'lastName'          => $input->getOption('lastName'),
            'email'             => $input->getOption('email'),
            'telephoneNumber'   => $input->getOption('telephoneNumber'),
            'campusId'          => $input->getOption('campusId'),
            'username'          => $input->getOption('username'),
            'password'          => $input->getOption('password'),
        ];

        $userRecord = $this->fillUserRecord($userRecord, $input, $output);

        $user = $this->userManager->findOneBy(['campusId' => $userRecord['campusId']]);
        if ($user) {
            throw new \Exception(
                'User #' . $user->getId() . " with campus id {$userRecord['campusId']} already exists."
            );
        }
        $user = $this->userManager->findOneBy(['email' => $userRecord['email']]);
        if ($user) {
            throw new \Exception(
                'User #' . $user->getId() . " with email address {$userRecord['email']} already exists."
            );
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('Campus ID', 'First', 'Last', 'Email', 'Username', 'Phone Number'))
            ->setRows(array(
                [
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['email'],
                    $userRecord['username'],
                    $userRecord['telephoneNumber']
                ]
            ))
        ;
        $table->render();
        
        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            "<question>Do you wish to add this user to Ilios in {$school->getTitle()}?</question>\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $user = $this->userManager->create();
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setEmail($userRecord['email']);
            $user->setCampusId($userRecord['campusId']);
            $user->setAddedViaIlios(true);
            $user->setEnabled(true);
            $user->setSchool($school);
            $user->setUserSyncIgnore(false);
            $this->userManager->update($user);

            /** @var AuthenticationInterface $authentication */
            $authentication = $this->authenticationManager->create();
            $authentication->setUsername($userRecord['username']);

            $user->setAuthentication($authentication);
            $sessionUser = $authentication->getSessionUser();

            $encodedPassword = $this->encoder->encodePassword($sessionUser, $userRecord['password']);
            $authentication->setPasswordBcrypt($encodedPassword);

            $this->authenticationManager->update($authentication);

            $output->writeln(
                '<info>Success! New user #' . $user->getId() . ' ' . $user->getFirstAndLastName() . ' created.</info>'
            );
        } else {
            $output->writeln('<comment>Canceled.</comment>');
        }
    }

    protected function fillUserRecord(array $userRecord, $input, $output)
    {
        if (empty($userRecord['firstName'])) {
            $userRecord['firstName'] = $this->askForString('First Name', 1, 20, $input, $output);
        }
        if (empty($userRecord['lastName'])) {
            $userRecord['lastName'] = $this->askForString('Last Name', 1, 30, $input, $output);
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
                    throw new \RuntimeException(
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
                    throw new \RuntimeException(
                        "Email is not valid"
                    );
                }
                return $answer;
            });
            $userRecord['email'] = $this->getHelper('question')->ask($input, $output, $question);
        }




        return $userRecord;
    }

    protected function askForString($what, $min, $max, $input, $output)
    {
        $question = new Question("What is the user's {$what}? ");
        $question->setValidator(function ($answer) use ($what, $min, $max) {
            if (strlen($answer) < $min) {
                throw new \RuntimeException(
                    "{$what} must be at least {$min} character"
                );
            }
            if (strlen($answer) >= $max) {
                throw new \RuntimeException(
                    "{$what} must be less than {$max} characters"
                );
            }
            return $answer;
        });
        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
