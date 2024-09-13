<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthenticationRepository;
use App\Repository\PendingUserUpdateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
#[AsCommand(
    name: 'ilios:sync-users',
    description: 'Sync all users against the directory by their campus ID.',
    aliases: ['ilios:directory:sync-users'],
)]
class SyncAllUsersCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository,
        protected PendingUserUpdateRepository $pendingUserUpdateRepository,
        protected Directory $directory,
        protected EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting User Sync Process.</info>');
        $this->userRepository->resetExaminedFlagForAllUsers();
        $this->pendingUserUpdateRepository->removeAllPendingUserUpdates();
        $campusIds = $this->userRepository->getAllCampusIds(false, false);
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
                $users = $this->userRepository->findBy([
                    'campusId' => $recordArray['campusId'],
                    'enabled' => true,
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
                        $this->userRepository->update($user, false);
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
                    $this->userRepository->update($user, false);
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
                        $pendingUpdate = $this->pendingUserUpdateRepository->create();
                        $pendingUpdate->setUser($user);
                        $pendingUpdate->setProperty('email');
                        $pendingUpdate->setValue($recordArray['email']);
                        $pendingUpdate->setType('emailMismatch');
                        $this->pendingUserUpdateRepository->update($pendingUpdate, false);
                    }
                }
                $computedFirstName = $recordArray['preferredFirstName'] ?? $recordArray['firstName'];
                //middle name falls back to null because it can be unset if not configured explicitly
                $computedMiddleName = $recordArray['preferredMiddleName'] ?? $recordArray['middleName'] ?? null;
                $computedLastName = $recordArray['preferredLastName'] ?? $recordArray['lastName'];

                if ($fixSmallThings && $user->getFirstName() != $computedFirstName) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating first name from "' . $user->getFirstName() .
                        '" to "' . $computedFirstName . '".</comment>'
                    );
                    $user->setFirstName($computedFirstName);
                }
                if ($fixSmallThings && $user->getMiddleName() != $computedMiddleName) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating middle name from "' . $user->getMiddleName() .
                        '" to "' . $computedMiddleName . '".</comment>'
                    );
                    $user->setMiddleName($computedMiddleName);
                }
                if ($fixSmallThings && $user->getLastName() != $computedLastName) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating last name from "' . $user->getLastName() .
                        '" to "' . $computedLastName . '".</comment>'
                    );
                    $user->setLastName($computedLastName);
                }
                if ($fixSmallThings && $user->getDisplayName() != $recordArray['displayName']) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating display name from "' . $user->getDisplayName() .
                        '" to "' . $recordArray['displayName'] . '".</comment>'
                    );
                    $user->setDisplayName($recordArray['displayName']);
                }
                if ($fixSmallThings && $user->getPronouns() != $recordArray['pronouns']) {
                    $update = true;
                    if ($recordArray['pronouns']) {
                        $output->writeln(
                            '  <comment>[I] Updating pronouns from "' . $user->getPronouns() .
                            '" to "' . $recordArray['pronouns'] . '".</comment>'
                        );
                    } else {
                        $output->writeln(
                            '  <comment>[I] Removing "' . $user->getPronouns() . '" pronoun.</comment>'
                        );
                    }
                    $user->setPronouns($recordArray['pronouns']);
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
                    $duplicate = $this->authenticationRepository->findOneBy(['username' => $recordArray['username']]);
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
                        $authentication = $this->authenticationRepository->create();
                        $authentication->setUser($user);
                    }
                }
                if (
                    $fixSmallThings &&
                    !$duplicateAuthenticationExists &&
                    $authentication->getUsername() != $recordArray['username']
                ) {
                    $update = true;
                    $output->writeln(
                        '  <comment>[I] Updating username from "' . $authentication->getUsername() .
                        '" to "' . $recordArray['username'] . '".</comment>'
                    );
                    $authentication->setUsername($recordArray['username']);
                    $this->authenticationRepository->update($authentication, false);
                }

                if ($update) {
                    $updated++;
                }
                $user->setExamined(true);
                $this->userRepository->update($user, false);
            }
            $this->em->flush();
            $this->em->clear();
        }
        $output->writeln('<info>Searching for users who were not examined during the sync process.</info>');

        $unsyncedUsers = $this->userRepository->findBy(
            ['examined' => false, 'enabled' => true, 'userSyncIgnore' => false],
            ['lastName' => ' ASC', 'firstName' => 'ASC']
        );
        $output->writeln('<info>Found ' . count($unsyncedUsers) . ' unexamined users.</info>');

        foreach ($unsyncedUsers as $user) {
            $campusId = $user->getCampusId() ? $user->getCampusId() : '(no campusId)';
            $output->writeln(
                '<comment>[I] User #' . $user->getId() . ' ' . $user->getFirstAndLastName() . ' ' .
                $user->getEmail() . ' ' . $campusId . ' ' .
                'not found in the directory.  Logged for further study.</comment>'
            );
            $update = $this->pendingUserUpdateRepository->create();
            $update->setUser($user);
            $update->setType('missingFromDirectory');
            $this->pendingUserUpdateRepository->update($update, false);
        }
        $this->em->flush();

        $output->writeln(
            "<info>Completed sync process {$totalRecords} users found in the directory; " .
            "{$updated} users updated.</info>"
        );

        return Command::SUCCESS;
    }

    protected function validateDirectoryRecord(array $record, OutputInterface $output): bool
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
