<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\PendingUserUpdateManager;
use Ilios\CoreBundle\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class SyncAllUsersCommand extends Command
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
     * @var PendingUserUpdateManager
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
        UserManager $userManager,
        AuthenticationManager $authenticationManager,
        PendingUserUpdateManager $pendingUserUpdateManager,
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
            ->setDescription('Sync all users against the directory by their campus ID.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting User Sync Process.</info>');
        $this->userManager->resetExaminedFlagForAllUsers();
        $this->pendingUserUpdateManager->removeAllPendingUserUpdates();
        $campusIds = $this->userManager->getAllCampusIds(false, false);
        $output->writeln(
            '<info>Attempting to update the ' .
            count($campusIds) .
            ' enabled and non sync ignored users in the system.</info>'
        );
        $output->writeln('<info>Searching the directory for users.</info>');
        $allUserRecords = $this->directory->findByCampusIds($campusIds);
        
        if (!$allUserRecords) {
            $output->writeln('<error>[E] Unable to find any users in the directory.</error>');
        }
        $totalRecords = count($allUserRecords);
        $output->writeln("<info>Found {$totalRecords} records in the directory.</info>");
        $updated = 0;
        $chunks = array_chunk($allUserRecords, 500);
        foreach ($chunks as $userRecords) {
            foreach ($userRecords as $recordArray) {
                $users = $this->userManager->findBy([
                    'campusId' => $recordArray['campusId'],
                    'enabled' => true,
                    'userSyncIgnore' => false,
                ]);
                if (count($users) == 0) {
                    //this shouldn't happen unless the user gets updated between
                    //listing all the IDs and getting results back from
                    //the directory
                    $output->writeln(
                        '<error>[E] Unable to find an enabled sync active user with ' .
                        'campus ID ' . $recordArray['campusId'] . '.</error>'
                    );
                    continue;
                }
                if (count($users) > 1) {
                    $output->writeln(
                        '<error>[E] Multiple accounts exist for the same ' .
                        'campus ID (' . $recordArray['campusId'] . ').  ' .
                        'None of them will be updated.</error>'
                    );
                    /* @var UserInterface $user */
                    foreach ($users as $user) {
                        $user->setExamined(true);
                        $this->userManager->update($user, false);
                    }
                    continue;
                }
                $user = $users[0];

                $update = false;
                $fixSmallThings = true;
                $output->writeln(
                    '<info>[I] Comparing User #' . $user->getId() . ' ' .
                    $user->getFirstAndLastName() . ' (' . $user->getEmail() . ') ' .
                    'to directory user by campus ID ' . $user->getCampusId() . '.</info>'
                );
                if (!$this->validateDirectoryRecord($recordArray, $output)) {
                    $user->setExamined(true);
                    $this->userManager->update($user, false);
                    //don't do anything else with invalid directory data
                    continue;
                }
                if ($user->getEmail() != $recordArray['email']) {
                    if (strtolower($user->getEmail()) == strtolower($recordArray['email'])) {
                        $update = true;
                        $output->writeln(
                            '  <comment>[I] Updating email from "' . $user->getEmail() .
                            '" to "' . $recordArray['email'] . '" since the only difference was the case.</comment>'
                        );
                        $user->setEmail($recordArray['email']);
                    } else {
                        $fixSmallThings = false;
                        $output->writeln(
                            '  <comment>[I] Email address "' . $user->getEmail() .
                            '" differs from "' . $recordArray['email'] . '" logging for further action.</comment>'
                        );
                        $pendingUpdate = $this->pendingUserUpdateManager->create();
                        $pendingUpdate->setUser($user);
                        $pendingUpdate->setProperty('email');
                        $pendingUpdate->setValue($recordArray['email']);
                        $pendingUpdate->setType('emailMismatch');
                        $this->pendingUserUpdateManager->update($pendingUpdate, false);
                    }
                }
                
                if ($fixSmallThings && $user->getFirstName() != $recordArray['firstName']) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating first name from "' . $user->getFirstName() .
                        '" to "' . $recordArray['firstName'] . '".</comment>'
                    );
                    $user->setFirstName($recordArray['firstName']);
                }
                if ($fixSmallThings && $user->getLastName() != $recordArray['lastName']) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating last name from "' . $user->getLastName() .
                        '" to "' . $recordArray['lastName'] . '".</comment>'
                    );
                    $user->setLastName($recordArray['lastName']);
                }
                if ($fixSmallThings && $user->getPhone() != $recordArray['telephoneNumber']) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating phone number from "' . $user->getPhone() .
                        '" to "' . $recordArray['telephoneNumber'] . '".</comment>'
                    );
                    $user->setPhone($recordArray['telephoneNumber']);
                }
                $authentication = $user->getAuthentication();
                $duplicateAuthenticationExists = false;
                if (!$authentication) {
                    $output->writeln(
                        '  <comment>[I] User had no authentication data, creating it now.</comment>'
                    );
                    /** @var AuthenticationInterface $duplicate */
                    $duplicate = $this->authenticationManager->findOneBy(['username' => $recordArray['username']]);
                    if ($duplicate) {
                        $duplicateAuthenticationExists = true;
                        $output->writeln(
                            '    <error>[E] There is already an account for username ' .
                            $recordArray['username'] . ' belonging to user #' . $duplicate->getUser()->getId() . ' ' .
                            'with Campus ID ' . $duplicate->getUser()->getCampusId() . ' ' .
                            'one of these users may be a duplicate or there may be an issue with the directory ' .
                            'no updates will be made to the authentication table at this time.</error>'
                        );
                    } else {
                        $authentication = $this->authenticationManager->create();
                        $authentication->setUser($user);
                    }
                }
                if ($fixSmallThings &&
                    !$duplicateAuthenticationExists &&
                    $authentication->getUsername() != $recordArray['username']) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating username from "' . $authentication->getUsername() .
                        '" to "' . $recordArray['username'] . '".</comment>'
                    );
                    $authentication->setUsername($recordArray['username']);
                    $this->authenticationManager->update($authentication, false);
                }
                
                if ($update) {
                    $updated++;
                }
                $user->setExamined(true);
                $this->userManager->update($user, false);
            }
            $this->em->flush();
            $this->em->clear();
        }
        $output->writeln('<info>Searching for users who were not examined during the sync process.</info>');
        
        $unsyncedUsers = $this->userManager->findBy(
            ['examined' => false, 'enabled' => true, 'userSyncIgnore' => false],
            ['lastName' => ' ASC', 'firstName' => 'ASC']
        );
        $output->writeln('<info>Found ' . count($unsyncedUsers) . ' unexamined users.</info>');
        
        foreach ($unsyncedUsers as $user) {
            $campusId = $user->getCampusId()?$user->getCampusId():'(no campusId)';
            $output->writeln(
                '<comment>[I] User #' . $user->getId() . ' ' . $user->getFirstAndLastName() . ' ' .
                $user->getEmail() . ' ' . $campusId . ' ' .
                'not found in the directory.  Logged for further study.</comment>'
            );
            $update = $this->pendingUserUpdateManager->create();
            $update->setUser($user);
            $update->setType('missingFromDirectory');
            $this->pendingUserUpdateManager->update($update, false);
        }
        $this->em->flush();

        $output->writeln(
            "<info>Completed sync process {$totalRecords} users found in the directory; " .
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
                    "  <error>[E] {$key} is required and it is missing from record with " .
                    'campus ID (' . $record['campusId'] . ').  ' .
                    'User will not be updated.</error>'
                );
            }
        }
        
        return $valid;
    }
}
