<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\Manager\UserRoleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Service\Directory;

/**
 * Adds all the users in the directory to a school with the student role
 *
 * Class AddNewStudentsToSchoolCommand
 */
class AddNewStudentsToSchoolCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @var AuthenticationManager
     */
    protected $authenticationManager;
    
    /**
     * @var UserRoleManager
     */
    protected $userRoleManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    public function __construct(
        UserManager $userManager,
        SchoolManager $schoolManager,
        AuthenticationManager $authenticationManager,
        UserRoleManager $userRoleManager,
        Directory $directory
    ) {
        $this->userManager = $userManager;
        $this->schoolManager = $schoolManager;
        $this->authenticationManager = $authenticationManager;
        $this->userRoleManager = $userRoleManager;
        $this->directory = $directory;
        
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
                'An LDAP filter to use in finding students who belong to the school in the directory.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        $schoolId = $input->getArgument('schoolId');
        $school = $this->schoolManager->findOneBy(['id' => $schoolId]);
        if (!$school) {
            throw new \Exception(
                "School with id {$schoolId} could not be found."
            );
        }
        $output->writeln("<info>Searching for new students to add to " . $school->getTitle() . ".</info>");
        
        $students = $this->directory->findByLdapFilter($filter);
        
        if (!$students) {
            $output->writeln("<error>{$filter} returned no results.</error>");
            return;
        }
        $output->writeln('<info>Found ' . count($students) . ' students in the directory.</info>');
        
        $campusIds = $this->userManager->getAllCampusIds();
        
        $newStudents = array_filter($students, function (array $arr) use ($campusIds) {
            return !in_array($arr['campusId'], $campusIds);
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
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $studentRole = $this->userRoleManager->findOneBy(array('title' => 'Student'));
            foreach ($newStudents as $userRecord) {
                if (empty($userRecord['email'])) {
                    $output->writeln(
                        '<error>Unable to add student ' .
                        var_export($userRecord, true) .
                        ' they have no email address.</error>'
                    );
                    continue;
                }
                if (empty($userRecord['campusId'])) {
                    $output->writeln(
                        '<error>Unable to add student ' .
                        var_export($userRecord, true) .
                        ' they have no campus ID.</error>'
                    );
                    continue;
                }
                if (empty($userRecord['username'])) {
                    $output->writeln(
                        '<error>Unable to add student ' .
                        var_export($userRecord, true) .
                        ' they have no username.</error>'
                    );
                    continue;
                }
                $user = $this->userManager->create();
                $user->setFirstName($userRecord['firstName']);
                $user->setLastName($userRecord['lastName']);
                $user->setEmail($userRecord['email']);
                $user->setCampusId($userRecord['campusId']);
                $user->setAddedViaIlios(true);
                $user->setEnabled(true);
                $user->setSchool($school);
                $user->setUserSyncIgnore(false);
                $user->addRole($studentRole);
                $this->userManager->update($user);
                
                $authentication = $this->authenticationManager->create();
                $authentication->setUser($user);
                $authentication->setUsername($userRecord['username']);
                $this->authenticationManager->update($authentication, false);
                
                $studentRole->addUser($user);
                $this->userRoleManager->update($studentRole);
                
                $output->writeln(
                    '<info>Success! New student #' .
                    $user->getId() . ' ' .
                    $user->getFirstAndLastName() .
                    ' created.</info>'
                );
            }
        } else {
            $output->writeln('<comment>Update canceled.</comment>');
        }
    }
}
