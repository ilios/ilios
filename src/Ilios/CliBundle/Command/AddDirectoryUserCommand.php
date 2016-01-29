<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Service\Directory;

/**
 * Add a user by looking them up in the directory
 *
 * Class AddDirectoryUserCommand
 * @package Ilios\CliBUndle\Command
 */
class AddDirectoryUserCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    
    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;
    
    /**
     * @var SchoolManagerInterface
     */
    protected $schoolManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    public function __construct(
        UserManagerInterface $userManager,
        AuthenticationManagerInterface $authenticationManager,
        SchoolManagerInterface $schoolManager,
        Directory $directory
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->schoolManager = $schoolManager;
        $this->directory = $directory;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:add-user')
            ->setDescription('Add a user to ilios.')
            ->addArgument(
                'campusId',
                InputArgument::REQUIRED,
                'The campus ID to lookup for adding the new user.'
            )
            ->addArgument(
                'schoolId',
                InputArgument::REQUIRED,
                'The primary school of the new user.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $campusId = $input->getArgument('campusId');
        $user = $this->userManager->findUserBy(['campusId' => $campusId]);
        if ($user) {
            throw new \Exception(
                'User #' . $user->getId() . " with campus id {$campusId} already exists."
            );
        }
        $schoolId = $input->getArgument('schoolId');
        $school = $this->schoolManager->findSchoolBy(['id' => $schoolId]);
        if (!$school) {
            throw new \Exception(
                "School with id {$schoolId} could not be found."
            );
        }
        
        $userRecord = $this->directory->findByCampusId($campusId);
        
        if (!$userRecord) {
            $output->writeln("<error>Unable to find campus ID {$campusId} in the directory.</error>");
            return;
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
            "<question>Do you wish to add this user to Ilios?</question>\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $user = $this->userManager->createUser();
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setEmail($userRecord['email']);
            $user->setCampusId($userRecord['campusId']);
            $user->setAddedViaIlios(true);
            $user->setEnabled(true);
            $user->setSchool($school);
            $user->setUserSyncIgnore(false);
            $this->userManager->updateUser($user);
            
            $authentication = $this->authenticationManager->createAuthentication();
            $authentication->setUser($user);
            $authentication->setUsername($userRecord['username']);
            $this->authenticationManager->updateAuthentication($authentication);

            $output->writeln(
                '<info>Success! New user #' . $user->getId() . ' ' . $user->getFirstAndLastName() . ' created.</info>'
            );
        } else {
            $output->writeln('<comment>Canceled.</comment>');
        }
        
        
    }
}
