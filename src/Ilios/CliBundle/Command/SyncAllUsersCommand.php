<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PendingUserUpdateManagerInterface;
use Ilios\CoreBundle\Service\Directory;

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
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var PendingUserUpdateManagerInterface
     */
    protected $pendingUserUpdateManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    public function __construct(
        UserManagerInterface $userManager,
        AuthenticationManagerInterface $authenticationManager,
        PendingUserUpdateManagerInterface $pendingUserUpdateManager,
        Directory $directory,
        EntityManager $em
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->pendingUserUpdateManager = $pendingUserUpdateManager;
        $this->directory = $directory;
        $this->em = $em;
        
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
        $this->pendingUserUpdateManager->removeAllPendingUserUpdates();
        $campusIds = $this->userManager->getAllCampusIds(false, false);
        $allUserRecords = $this->directory->findByCampusIds($campusIds);
        
        if (!$allUserRecords) {
            $output->writeln('<error>Unable to find any users in the directory</error>');
        }
        $totalRecords = count($allUserRecords);
        $updated = 0;
        $chunks = array_chunk($allUserRecords, 500);
        foreach ($chunks as $userRecords) {
            foreach ($userRecords as $recordArray) {
                $users = $this->userManager->findUsersBy([
                    'campusId' => $recordArray['campusId'],
                    'enabled' => true,
                    'userSyncIgnore' => false
                ]);
                if (count($users) == 0) {
                    //this shouldn't happen unless the user gets updated between
                    //listing all the IDs and getting results back from
                    //the directory
                    $output->writeln(
                        '<error>Unable to find an active sync user with ' .
                        'Campus ID ' . $recordArray['campusId'] . '</error>'
                    );
                    continue;
                }
                if (count($users) > 1) {
                    $output->writeln(
                        '<error>Multiple accounts exist for the same ' .
                        'Campus ID (' . $recordArray['campusId'] . ').  ' .
                        'None of them will be updated.</error>'
                    );
                    foreach ($users as $user) {
                        $user->setExamined(true);
                        $this->userManager->updateUser($user, false);
                    }
                    continue;
                }
                $user = $users[0];

                $update = false;
                $output->writeln(
                    '<info>Comparing User #' . $user->getId() . ' ' .
                    $user->getFirstAndLastName() . ' (' . $user->getEmail() . ') ' .
                    'to directory user by Campus ID ' . $user->getCampusId() . '</info>'
                );
                if (!$this->validateDirectoryRecord($recordArray, $output)) {
                    $user->setExamined(true);
                    $this->userManager->updateUser($user, false);
                    //don't do anything else with invalid directory data
                    continue;
                }
                if ($user->getFirstName() != $recordArray['firstName']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating first name from "' . $user->getFirstName() .
                        '" to "' . $recordArray['firstName'] . '"</comment>'
                    );
                    $user->setFirstName($recordArray['firstName']);
                }
                if ($user->getLastName() != $recordArray['lastName']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating last name from "' . $user->getLastName() .
                        '" to "' . $recordArray['lastName'] . '"</comment>'
                    );
                    $user->setLastName($recordArray['lastName']);
                }
                if ($user->getPhone() != $recordArray['telephoneNumber']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating phone number from "' . $user->getPhone() .
                        '" to "' . $recordArray['telephoneNumber'] . '"</comment>'
                    );
                    $user->setPhone($recordArray['telephoneNumber']);
                }
                if ($user->getEmail() != $recordArray['email']) {
                    if (strtolower($user->getEmail() == strtolower($recordArray['email']))) {
                        $update = true;
                        $output->writeln(
                            '<comment>Updating email from "' . $user->getEmail() .
                            '" to "' . $recordArray['email'] . '" since the only difference was the case.</comment>'
                        );
                        $user->setEmail($recordArray['email']);
                    } else {
                        $output->writeln(
                            '<comment>Email address "' . $user->getEmail() .
                            '" differs from "' . $recordArray['email'] . '" logging for further action.</comment>'
                        );
                        $update = $this->pendingUserUpdateManager->createPendingUserUpdate();
                        $update->setUser($user);
                        $update->setProperty('email');
                        $update->setValue($recordArray['email']);
                        $update->setType('emailMismatch');
                        $this->pendingUserUpdateManager->updatePendingUserUpdate($update, false);
                    }
                }
                
                $authentication = $user->getAuthentication();
                if (!$authentication) {
                    $output->writeln(
                        '<comment>User had no Authentication data, creating it now.</comment>'
                    );
                    $authentication = $this->authenticationManager->createAuthentication();
                    $authentication->setUser($user);
                }
                if ($authentication->getUsername() != $recordArray['username']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating username from "' . $authentication->getUsername() .
                        '" to "' . $recordArray['username'] . '"</comment>'
                    );
                    $authentication->setUsername($recordArray['username']);
                    $this->authenticationManager->updateAuthentication($authentication, false);
                }
                
                if ($update) {
                    $updated++;
                }
                $user->setExamined(true);
                $this->userManager->updateUser($user, false);
            }
            $this->em->flush();
            $this->em->clear();
        }
        
        $unsyncedUsers = $this->userManager->findUsersBy(
            ['examined' => false, 'enabled' => true, 'userSyncIgnore' => false]
        );
        foreach ($unsyncedUsers as $user) {
            $output->writeln(
                '<comment>User not found in the directory.  Logged for further study.</comment>'
            );
            $update = $this->pendingUserUpdateManager->createPendingUserUpdate();
            $update->setUser($user);
            $update->setType('missingFromDirectory');
            $this->pendingUserUpdateManager->updatePendingUserUpdate($update, false);
        }
        $this->em->flush();

        $output->writeln(
            "<info>Completed Sync Process {$totalRecords} users found in the directory; " .
            "{$updated} users updated.</info>"
        );
        
    }
    
    protected function validateDirectoryRecord(array $record, OutputInterface $output)
    {
        $valid = true;
        $requiredFields = ['firstName', 'lastName', 'email', 'username'];
        foreach ($requiredFields as $key) {
            if (empty($record[$key])) {
                $valid = false;
                $output->writeln(
                    "<error> {$key} is required and it is missing from record with " .
                    'Campus ID (' . $record['campusId'] . ').  ' .
                    'User will not be updated.</error>'
                );
            }
        }
        
        return $valid;
    }
}
