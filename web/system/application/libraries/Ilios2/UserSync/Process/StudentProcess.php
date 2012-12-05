<?php

/**
 * The Student-user synchronization process.
 */
class Ilios2_UserSync_Process_StudentProcess extends Ilios2_UserSync_Process
{
    /**
     * @var Ilios2_UserSync_UserSource
     */
    protected $_userSource;

    /**
     * @var User
     */
    protected $_userDao;

    /**
     * @var School
     */
    protected $_schoolDao;


    /**
     * The name of this process, should be unique within the system.
     * @var string
     */
    const SIGNATURE = 'Ilios2_UserSync_Process_StudentProcess';

    /**
     * Constructor.
     * @param Ilios2_UserSync_UserSource $source
     * @param User $userModel
     * @param School $schoolModel
     * @param User_Sync_Exception $syncExceptionDao
     * @param array $config process configuration
     */
    public function __construct (Ilios2_UserSync_UserSource $userSource, User $userDao,
                                    School $schoolDao, User_Sync_Exception $syncExceptionDao)
    {
        parent::__construct($syncExceptionDao, self::SIGNATURE);
        $this->_userSource = $userSource;
        $this->_userDao = $userDao;
        $this->_schoolDao = $schoolDao;
    }

    /**
     * Process implementation.
     * @param int $processId
     * @param Ilios2_Logger $logger
     * @return boolean TRUE on success, FALSE otherwise
     */
    protected function _run ($processId, Ilios2_Logger $logger)
    {
        $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE , $processId);
        $msg = "Kicking off the Student-user synchronization process, here we go ....";
        $logger->log($msg , $processId);
        // ----------------------
        // 0. Initalization
        // ----------------------
        // remove all recorded sync exceptions from the last run
        $this->_deleteUserSyncExceptionsForProcess();
        // reset the "examined" bit on all student records
        try {
            $this->_resetExaminedFlagOnStudents();
        } catch (Ilios2_UserSync_Exception $e) {
            // log borkage
            $logger->log($e->getMessage(), $processId, 0, Ilios2_Logger::LOG_LEVEL_ERROR);
            return false;
        }

        // ----------------------
        // 1. Student ingestion from internal source
        // ----------------------
        try {
            // counters
            $externalStudentsCount = 0;
            $addedStudentsCount = 0;
            $disabledStudentsCount = 0;
            $updatedStudentsCount = 0;

            // get all student users from the external user source
            $externalStudents = $this->_userSource->getAllStudentRecords();

            // load all school ids
            $schoolIds = $this->_schoolDao->getAllSchools();

            // count the external users
            $externalStudentsCount = count($externalStudents);

            $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE, $processId);
            $msg = 'Processing inbound/external student-records';
            $logger->log($msg, $processId);
            $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE, $processId);

            // iterate over the retrieved students
            foreach ($externalStudents as $externalStudent) {
                $logger->log("Processing external user record " . $externalStudent  , $processId);
                // validate the incoming student record
                try {
                    $this->_validateIncomingStudentRecord($externalStudent, $schoolIds);
                } catch (Ilios2_UserSync_Process_ExternalUserException $e2) {
                    // for now, we just log these exceptions and continue
                    $logger->log($e2->getMessage(), $processId, 1, Ilios2_Logger::LOG_LEVEL_ERROR);
                    continue; // do not process these users any further!
                }
                if ($this->_hasMatchingUsersInIlios($externalStudent)) {
                    $enabledUsers = $this->_getMatchingEnabledUsersFromIlios ($externalStudent);
                    if (! count($enabledUsers)) {
                        // edge case:
                        // the external student has matching enabled student-users in Ilios,
                        // but all of them are either disabled or flagged to be ignored by the sync process
                        // make a note of that.
                        $msg = 'Matching users found in Ilios, but all are flagged to be ignored or disabled.';
                        $logger->log($msg, $processId, 1);
                        continue;

                    }
                    foreach ($enabledUsers as $enabledUser) {
                        try {
                            // match each enabled user against the external one
                            // determine any mismatches
                            $this->_matchStudentAgainstUser($externalStudent, $enabledUser);
                        } catch (Ilios2_UserSync_Process_UserException $e2) {
                            // mismatch detected!
                            // save it for later review and processing
                            $this->_saveUserSyncException($processId, $enabledUser['user_id'], $e2);
                            $logger->log($e2->getMessage(), $processId, 1, Ilios2_Logger::LOG_LEVEL_WARN);
                            // flag user as examined.
                            $this->_userDao->setUserExaminedBit($enabledUser['user_id'], true);
                            continue;
                        }
                        // update the Ilios-internal user record
                        $this->_updateUser($externalStudent, $enabledUser);
                        $updatedStudentsCount++;
                        // log the update event!
                        $msg = "Updated user. (Ilios user id: {$enabledUser['user_id']}, UID: {$enabledUser['uc_uid']})";
                        $logger->log($msg, $processId, 1);
                    }
                } else { // add new user record
                    try {
                        $newUserId = $this->_addStudentToIlios($externalStudent);
                        $addedStudentsCount++; // increment counter
                        // make note of new user addition in log
                        $msg = "Added: {$externalStudent} -- Ilios db id: {$newUserId}";
                        $logger->log($msg, $processId, 1);
                    } catch (Ilios2_UserSync_Exception $e2) {
                        // log the incident
                        $logger->log($e2->getMessage(), $processId, 1, Ilios2_Logger::LOG_LEVEL_ERROR);
                    }
                }
            }
        } catch (Ilios2_UserSync_Exception $e) {
            // Something blew up while reading from the external user store.
            // There's nothing that can be done to recover from such an error.
            // Log the incident and abort processing.
            $logger->log($e->getMessage(), $processId, 0, Ilios2_Logger::LOG_LEVEL_ERROR);
            return false;
        }


        // ----------------------------------
        // 2. Process exception handling
        // ----------------------------------

        // if we've come this far then it's time to
        // process any remaining student records in Ilios
        // that did not get flagged as examined.
        $disabledStudentsCount = $this->_processUnexaminedStudents($processId, $logger);

        // ----------------------------------
        // 3. Post-run Processing
        // ----------------------------------
        $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE , $processId);
        $msg  = "Completed student sync process.";
        $logger->log($msg, $processId);
        $msg  = "# of external students processed: {$externalStudentsCount}";
        $logger->log($msg, $processId);
        $msg = "# of students added: {$addedStudentsCount}";
        $logger->log($msg, $processId);
        $msg = "# of students updated: {$updatedStudentsCount}";
        $logger->log($msg, $processId);
        $msg = "# of students disabled: {$disabledStudentsCount}";
        $logger->log($msg, $processId);
        $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE , $processId);
        return true;
    }

    /**
     * Resets the 'examined' flag on all student records on file.
     * @throws Ilios2_UserSync_Exception
     */
    protected function _resetExaminedFlagOnStudents ()
    {
        $failedTransaction = true;
        $transactionRetryCount = Ilios2_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $this->_userDao->startTransaction();
            $this->_userDao->clearUsersExaminedBit(true);
            if ($this->_userDao->transactionAtomFailed()) {
                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->_userDao);
            } else {
                $failedTransaction = false;
                $this->_userDao->commitTransaction();
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        if ($failedTransaction) {
            throw new Ilios2_UserSync_Exception("Failed to reset 'examined' flag on existing users.");
        }
    }


    /**
     * Validates an incoming student record from an external user source.
     * 1. Check if the user has a valid ID
     * 2. Check if the user has a valid email address
     * 3. Check if the user can be associated to one of the schools in the system
     * If at least one of the criteria above is not met then an exception is thrown.
     * @param Ilios2_UserSync_ExternalUser $student
     * @param array $schoolIds the ids of all school records in the system
     * @throws Ilios2_UserSync_Process_ExternalUserException
     */
    protected function _validateIncomingStudentRecord (Ilios2_UserSync_ExternalUser $student, array $schoolIds)
    {
        $uid = $student->getUid();
        if (empty($uid)) {
            throw new Ilios2_UserSync_Process_ExternalUserException(
            			'Missing UID for inbound student: ' . $student,
                        Ilios2_UserSync_Process_ExternalUserException::INVALID_UID);
        }
        $email = $student->getEmail();
        if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Ilios2_UserSync_Process_ExternalUserException(
            			'Missing or invalid email for inbound student: ' . $student,
                        Ilios2_UserSync_Process_ExternalUserException::INVALID_EMAIL);
        }
        $schoolId = $student->getSchoolId();
        if (empty($schoolId) || ! in_array($schoolId, $schoolIds)) {
            throw new Ilios2_UserSync_Process_ExternalUserException(
            			'Missing or invalid school id for inbound student: ' . $student,
                        Ilios2_UserSync_Process_ExternalUserException::INVALID_PRIMARY_SCHOOL_ID);
        }
    }


    /**
     * Checks if one or more enabled user with the same UID as the given external user
     * already exist in Ilios.
     * Please note that this _includes_ disabled users and users that are flagged to be ignored in the sync process.
     * @param Ilios2_UserSync_ExternalUser $externalUser
     * @return boolean TRUE if user(s) exist(s), FALSE otherwise
     */
    protected function _hasMatchingUsersInIlios (Ilios2_UserSync_ExternalUser $externalUser)
    {
        return $this->_userDao->hasUsersWithUid($externalUser->getUid(), false, false);
    }

    /**
     * Adds a user as student to Ilios.
     * @param Ilios2_UserSync_ExternalUser $externalUser
     * @return int the id for the newly created user record
     * @throws Ilios2_UserSync_Exception
     */
    protected function _addStudentToIlios (Ilios2_UserSync_ExternalUser $externalUser)
    {
        $newUserId = $this->_userDao->addUserAsStudent($externalUser->getLastName(),
                                 $externalUser->getFirstName(),
                                 $externalUser->getMiddleName(),
                                 $externalUser->getPhone(),
                                 $externalUser->getEmail(),
                                 $externalUser->getUid(),
                                 null,
                                 null, // this NULL-assignment for cohort is important!
                                 $externalUser->getSchoolId());
        if (-1 == $newUserId) {
            throw new Ilios2_UserSync_Exception('Failed to add external user as student to Ilios' . $externalUser);
        }

        $this->_userDao->enableUser($newUserId, false); // disable user record
        $this->_userDao->setUserExaminedBit($newUserId, true); // flag user as examined

        return $newUserId;
    }

    /**
     * Processes 'unexamined' students and flags them for review/disables them where applicable.
     * @param int $processId
     * @param Ilios2_Logger $logger
     * @return int the total number of students that got disabled
     */
    protected function _processUnexaminedStudents ($processId, Ilios2_Logger $logger)
    {
        $c = 0; // counter for students that get disabled in the process
        $unexaminedStudents = $this->_userDao->getUnexaminedUsers(true, true);

        $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE, $processId);
        $msg = 'Processing "unexamined" students';
        $logger->log($msg, $processId);
        $logger->log(Ilios2_Logger::LOG_SEPARATION_LINE, $processId);
        if (! count($unexaminedStudents)) {
            $logger->log('No "unexamined" students found.', $processId);
            return 0;
        }
        // iterate over unexamined student records
        foreach ($unexaminedStudents as $student) {
            $disableUser = false; // reset disable flag
            $msg = 'Processing Ilios user record with user_id = ' . $student['user_id'];
            $logger->log($msg, $processId);
            // flag student as examined
            $this->_userDao->setUserExaminedBit($student['user_id'], true);
            // check if the user exists AS STUDENT in the external store
            // if not then flag him/her for review indicating the discrepancy.
            // otherwise, flag him for review, indicating an unknown error.
            try {
                $externalStudentExists = $this->_userSource->hasStudent($student['uc_uid']);
                try {
                    if (! $externalStudentExists) {
                        $externalUserExists = $this->_userSource->hasUser($student['uc_uid']);
                        // User exists in external user store, but not as student.
                        // Make note of it but don't disable this user.
                        if ($externalUserExists) {
                            $msg = "User exists in external user store, but not as a student.";
                            $msg .= " (Ilios user id: {$student['user_id']}, UID: {$student['uc_uid']})";
                            throw new Ilios2_UserSync_Process_UserException($msg,
                                    Ilios2_UserSync_Process_UserException::STUDENT_SYNC_STATUS_MISMATCH_IN_EXTERNAL_USER_STORE);
                        }

                        // User not found in external user store.
                        // Disable that user in Ilios.
                        $disableUser = true;
                        $msg = "User not found in external user store.";
                        $msg .= " (Ilios user id: {$student['user_id']}, UID: {$student['uc_uid']})";
                        throw new Ilios2_UserSync_Process_UserException($msg,
                                    Ilios2_UserSync_Process_UserException::STUDENT_SYNC_NOT_IN_EXTERNAL_USER_SOURCE);
                    }
                    // student found in external user store
                    // something else must be wrong
                    // disable that user
                    $disableUser = true;
                    $msg = "Student did not get examined during user sync for reasons unknown.";
                    $msg .= " (Ilios user id: {$student['user_id']}, UID: {$student['uc_uid']})";
                    throw new Ilios2_UserSync_Process_UserException($msg,
                                Ilios2_UserSync_Process_UserException::STUDENT_SYNC_UNKNOWN_ERROR);
                } catch (Ilios2_UserSync_Process_UserException $e) {
                    $this->_saveUserSyncException($processId, $student['user_id'], $e);
                    $logger->log($e->getMessage(), $processId, 1, Ilios2_Logger::LOG_LEVEL_WARN);
                }
            } catch (Ilios2_Ldap_Exception $e) {
                $msg = "Failed to verify existence of unexamined student in external user store.";
                $msg .= " (Ilios user id: {$student['user_id']}, UID: {$student['uc_uid']})";
                $logger->log($msg, $processId, 1, Ilios2_Logger::LOG_LEVEL_ERROR);
                continue; // move on to the next user
            }
            // disable user if applicable
            if ($disableUser) {
                $this->_userDao->enableUser($student['user_id'], false);
                $c++; // increase counter
                // log it!
                $msg = "Disabled user. (Ilios user id: {$student['user_id']}, UID: {$student['uc_uid']})";
                $logger->log($msg, $processId, 1);
            }
        }
        return $c;
    }

    /**
     * Retrieves all enabled user accounts with an UID matching the one from a given external user.
      * Please note that this _excludes_ users that are flagged to be ignored in the sync process.
     * @param Ilios2_UserSync_ExternalUser $externalUser
     * @return array nested array of user records
     */
    protected function _getMatchingEnabledUsersFromIlios (Ilios2_UserSync_ExternalUser $externalUser)
    {
        return $this->_userDao->getUsersWithUid($externalUser->getUid(), true, true);
    }

    /**
     * Matches a given user record from Ilios against an external student record.
     * Throws an exception if a mismatch is detected.
     * @param Ilios2_UserSync_ExternalUser $externalUser
     * @param array $user
     * @throws Ilios2_UserSync_Process_UserException
     */
    protected function _matchStudentAgainstUser (Ilios2_UserSync_ExternalUser $externalUser, array $user)
    {
        // validation magic goes here
        // 1. check for email mis/match
        if (0 !== strcasecmp($externalUser->getEmail(), $user['email'])) {
            $msg = "Ext. user email <{$externalUser->getEmail()}> does not match Ilios user email <{$user['email']}>.";
            $msg .= " (Ilios user id: {$user['user_id']}, UID: {$user['uc_uid']})";
            throw new Ilios2_UserSync_Process_UserException($msg,
                            Ilios2_UserSync_Process_UserException::STUDENT_SYNC_EMAIL_MISMATCH,
                            'email', $externalUser->getEmail());
        }
        // 2. check for student status mis/match
        $isUserStudent = $this->_userDao->userIsStudent($user['user_id']);
        $isExtUserStudent = $externalUser->isStudent();
        if ($isExtUserStudent != $isUserStudent) {
            $msg = 'Ext. user is ' . ($isExtUserStudent ? '' : 'not ') . 'a student,';
            $msg .= ' but Ilios user is ' . ( $isUserStudent ? '' : 'not ') . 'a student.';
            $msg .= " (Ilios user id: {$user['user_id']}, UID: {$user['uc_uid']})";
            throw new Ilios2_UserSync_Process_UserException($msg,
                $isExtUserStudent ? Ilios2_UserSync_Process_UserException::STUDENT_SYNC_STATUS_MISMATCH_IN_ILIOS
                    : Ilios2_UserSync_Process_UserException::STUDENT_SYNC_STATUS_MISMATCH_IN_EXTERNAL_USER_STORE);
        }
    }

    /**
     * Update a given user's properties with the properties
     * of a given corresponding external user record.
     * Then flag this user as 'examined'.
     * @param Ilios2_UserSync_ExternalUser $externalUser
     * @param array $user
     */
    protected function _updateUser (Ilios2_UserSync_ExternalUser $externalUser, array $user)
    {
        // update user record
        $this->_userDao->updateUser($user['user_id'],
             $externalUser->getFirstName(),
             $externalUser->getMiddleName(),
             $externalUser->getLastName(),
             true,
             $externalUser->getPhone());

        // flag user as examined
        $this->_userDao->setUserExaminedBit($user['user_id'], true);
    }
}
