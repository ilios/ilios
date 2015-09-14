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
class SyncAllUsersCommand extends Command
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
            ->setName('ilios:directory:sync-users')
            ->setDescription('Sync all users against the directory by their campus id.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userManager->resetExaminedFlagForAllUsers();
        $campusIds = $this->userManager->getAllCampusIds(false, false);
        $userRecords = $this->directory->findByCampusIds($campusIds);
        
        if (!$userRecords) {
            $output->writeln('<error>Unable to find any users in the directory');
            return;
        }
        $totalRecords = count($userRecords);
        $updated = 0;
        foreach ($userRecords as $recordArray) {
            $user = $this->userManager->findUserBy([
                'campusId' => $recordArray['campusId'],
                'enabled' => true,
                'userSyncIgnore' => false
            ]);
            if (!$user) {
                //this shouldn't happen unless the user gets updated between
                //listing all the IDs on line 68 and getting them back from
                //the directory
                $output->writeln(
                    '<error>Unable to find an active sync user with ' .
                    'campus id ' . $recordArray['campusId'] . '</error>'
                );
                continue;
            }
            $update = false;
            $output->writeln(
                '<info>Comparing User #' . $user->getId() . ' ' .
                $user->getFirstAndLastName() . ' to directory user by campus id ' .
                $user->getCampusId() . '</info>'
            );
            if ($user->getFirstName() != $recordArray['firstName']) {
                $update = true;
                $user->setFirstName($recordArray['firstName']);
                $output->writeln(
                    '<info>Updating first name from "' . $user->getFirstName() .
                    '" to "' . $recordArray['firstName'] . '"</info>'
                );
            }
            if ($user->getLastName() != $recordArray['lastName']) {
                $update = true;
                $user->setLastName($recordArray['lastName']);
                $output->writeln(
                    '<info>Updating last name from "' . $user->getLastName() .
                    '" to "' . $recordArray['lastName'] . '"</info>'
                );
            }
            if ($user->getPhone() != $recordArray['telephoneNumber']) {
                $update = true;
                $user->setPhone($recordArray['telephoneNumber']);
                $output->writeln(
                    '<info>Updating phone number from "' . $user->getPhone() .
                    '" to "' . $recordArray['telephoneNumber'] . '"</info>'
                );
            }
            if ($update) {
                $updated++;
            }
            $user->setExamined(true);
            $this->userManager->updateUser($user, false);
        }
        
        $output->writeln(
            "<info>Completed Sync Process {$totalRecords} users found in the directory; " .
            "{$updated} users updated.</info>"
        );
        
    }
}
