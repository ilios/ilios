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
use Ilios\CoreBundle\Service\Directory;
use Ilios\AuthenticationBundle\Service\AuthenticationInterface;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 * @package Ilios\CliBUndle\Command
 */
class SyncUserCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    
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
        Directory $directory,
        AuthenticationInterface $authenticationService
    ) {
        $this->userManager = $userManager;
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
            ->setName('ilios:directory:sync-user')
            ->setDescription('Sync a user from the directory.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        $user = $this->userManager->findUserBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        
        $userRecord = $this->directory->findByCampusId($user->getCampusId());
        
        if (!$userRecord) {
            $output->writeln('<error>Unable to find ' . $user->getCampusId() . ' in the directory');
            return;
        }
        
        $table = new Table($output);
        $table
            ->setHeaders(array('Record', 'Campus ID', 'First', 'Last', 'Email', 'Phone Number'))
            ->setRows(array(
                [
                    'Ilios User',
                    $user->getCampusId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getEmail(),
                    $user->getPhone()
                ],
                [
                    'Directory User',
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['email'],
                    $userRecord['telephoneNumber']
                ]
            ))
        ;
        $table->render();
        
        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to update this Ilios User with the data ' .
            'from the Directory User? </question>',
            false
        );
        
        if ($helper->ask($input, $output, $question)) {
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setEmail($userRecord['email']);
            $user->setPhone($userRecord['telephoneNumber']);
            $this->authenticationService->syncUser($userRecord, $user);
            $this->userManager->updateUser($user);
            
            $output->writeln('<info>User Updated Successfully</info>');
        } else {
            $output->writeln('<comment>Update Canceled</comment>');
        }
        
    }
}
