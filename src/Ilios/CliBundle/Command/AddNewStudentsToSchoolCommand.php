<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Directory;
use Ilios\AuthenticationBundle\Service\AuthenticationInterface;

/**
 * Adds all the users in the directory to a school with the student role
 *
 * Class AddNewStudentsToSchoolCommand
 * @package Ilios\CliBUndle\Command
 */
class AddNewStudentsToSchoolCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var SchoolManagerInterface
     */
    protected $schoolManager;
    
    /**
     * @var UserRoleManagerInterface
     */
    protected $userRoleManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    /**
     * @var AuthenticationInterface
     */
    protected $authenticationService;
    
    public function __construct(
        UserManagerInterface $userManager,
        SchoolManagerInterface $schoolManager,
        UserRoleManagerInterface $userRoleManager,
        Directory $directory,
        AuthenticationInterface $authenticationService
    ) {
        $this->userManager = $userManager;
        $this->schoolManager = $schoolManager;
        $this->userRoleManager = $userRoleManager;
        $this->directory = $directory;
        $this->authenticationService = $authenticationService;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:add-students')
            ->setDescription('Add students found by a directory filter into a school.')
            ->addArgument(
                'schoolId',
                InputArgument::REQUIRED,
                'Which school ID to add new students to.'
            )
            ->addArgument(
                'filter',
                InputArgument::REQUIRED,
                'An LDAP filter to use in finding students who belong to the school in the direcotry.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        $schoolId = $input->getArgument('schoolId');
        $school = $this->schoolManager->findSchoolBy(['id' => $schoolId]);
        if (!$school) {
            throw new \Exception(
                "School with id {$schoolId} could not be found."
            );
        }
        
        $students = $this->directory->findByLdapFilter($filter);
        
        if (!$students) {
            $output->writeln("<error>{$filter} returned no results.</error>");
            return;
        }
        $output->writeln('<info>Found ' . count($students) . ' students in the directory.</info>');
        
        $campusIds = $this->userManager->getAllCampusIds();
        
        $newStudents = array_filter($students, function (array $arr) use ($campusIds) {
            return !$campusIds->contains($arr['campusId']);
        });
        
        if (!count($newStudents) > 0) {
            $output->writeln("<info>There are no new students to add.</info>");
            return;
        }
        $output->writeln(
            '<info>There are ' .
            count($newStudents) .
            ' new students to be added to ' .
            $school->getTitle() .
            '.</info>'
        );
        $rows =array_map(function (array $arr) {
            return [
                $arr['campusId'],
                $arr['firstName'],
                $arr['lastName'],
                $arr['email']
            ];
        }, $newStudents);
        $table = new Table($output);
        $table->setHeaders(array('Campus ID', 'First', 'Last', 'Email'))->setRows($rows);
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to add these students to ' . $school->getTitle() . '? </question>' . "\n",
            false
        );
        
        if ($helper->ask($input, $output, $question)) {
            $studentRole = $this->userRoleManager->findUserRoleBy(array('title' => 'Student'));
            foreach ($newStudents as $userRecord) {
                if (empty($userRecord['email'])) {
                    $output->writeln(
                        '<error>Unable to add student ' .
                        var_export($userRecord, true) .
                        ' they have no email address</error>'
                    );
                    continue;
                }
                if (empty($userRecord['campusId'])) {
                    $output->writeln(
                        '<error>Unable to add student ' .
                        var_export($userRecord, true) .
                        ' they have no campus ID</error>'
                    );
                    continue;
                }
                $user = $this->userManager->createUser();
                $user->setFirstName($userRecord['firstName']);
                $user->setLastName($userRecord['lastName']);
                $user->setEmail($userRecord['email']);
                $user->setCampusId($userRecord['campusId']);
                $user->setAddedViaIlios(true);
                $user->setEnabled(true);
                $user->setSchool($school);
                $user->setUserSyncIgnore(false);
                //persist the user so it can be used in setupUser method
                $this->userManager->updateUser($user);

                $this->authenticationService->setupNewUser($userRecord, $user);
                $studentRole->addUser($user);
                $user->addRole($studentRole);
                //save again in case setupUser made any changes
                $this->userManager->updateUser($user, false);

                $output->writeln(
                    '<info>Success! New student #' .
                    $user->getId() . ' ' .
                    $user->getFirstAndLastName() .
                    ' created.</info>'
                );
            }
            $this->userRoleManager->updateUserRole($studentRole);
        } else {
            $output->writeln('<comment>Update Canceled</comment>');
        }
        
    }
}
