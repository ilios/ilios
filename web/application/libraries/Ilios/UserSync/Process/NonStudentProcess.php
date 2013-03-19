<?php

/**
 * Represents the Non-student-user synchronization process.
 */
class Ilios_UserSync_Process_NonStudentProcess extends Ilios_UserSync_Process
{
    /* constants */
    /**
     * Indicator that "UID" is the primary match-making attribute between external and Ilios users.
     * @var string
     */
    const MATCH_ILIOS_USERS_BY_UID = 'uid';

    /**
     * Indicator that "Email" is the primary match-making attribute between external and Ilios users.
     * @var string
     */
    const MATCH_ILIOS_USERS_BY_EMAIL = 'email';

    /**
     * The name of this process, should be unique within the system.
     * @var string
     */
    const SIGNATURE = 'Ilios_UserSync_Process_NonStudentProcess';

    /**
     * @var Ilios_UserSync_UserSource
     */
    protected $_userSource;

    /**
     * @var User
     */
    protected $_userDao;

    /**
     * Constructor.
     * @param Ilios_UserSync_UserSource $source
     * @param User $userModel
     * @param User_Sync_Exception $syncExceptionDao
     * @param array $config process configuration
     */
    public function __construct (Ilios_UserSync_UserSource $userSource, User $userDao,
                                     User_Sync_Exception $syncExceptionDao)
    {
        parent::__construct($syncExceptionDao, self::SIGNATURE);
        $this->_userSource = $userSource;
        $this->_userDao = $userDao;
    }


    /**
     * Process implementation.
     * @param int $processId
     * @param Ilios_Logger $logger
     * @return boolean TRUE on success, FALSE on error
     */
    protected function _run($processId, Ilios_Logger $logger)
    {
        $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
        $msg = "Kicking off the Non-student-user synchronization process, here we go ....";
        $logger->log($msg , $processId);
        $nonStudentUserCount = 0;
        $updatedUsersCount = 0;
        $processedUsersCount = 0;
        // -----------------------------
        // 0. Initalization
        // ----------------------------
        // remove any previously recorded sync exceptions
        $this->_deleteUserSyncExceptionsForProcess();

        // ----------------------------
        // 1. pull all enabled non-students that are not flagged to be ignored
        // ----------------------------
        $nonStudents = $this->_getEnabledNonStudents();
        $nonStudentUserCount = count($nonStudents);
        if (! $nonStudentUserCount) {
            $msg = 'No eligible non-students found for synchronization.';
            $logger->log($msg , $processId);
            $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
            return true; // we're done
        }

        // ----------------------------------------
        // 2. elaborate matching of each non-student user against external user store
        // ----------------------------------------
        foreach ($nonStudents as $nonStudent) {
            $processedUsersCount++;
            $msg = "Processing Ilios non-student user (user_id : {$nonStudent['user_id']} , uid : {$nonStudent['uc_uid']} , email : {$nonStudent['email']})";
            $logger->log($msg, $processId);
            $updated = false;
            // ----------------------------------------
            // 2a. match by UID
            // ----------------------------------------

            // attempt to find any external users matching the current ilios non-student by UID
            try {
                $extUsers = $this->_userSource->getUserByUid($nonStudent['uc_uid']);
                $userMatch = false;
                $extUsersCount = count($extUsers);
                switch ($extUsersCount) {
                    case 0 :
                        // no match, proceed with plan (b)
                        // do nothing here
                        break;
                    case 1 :
                        // exactly one match
                        // set $userMatch flag
                        $userMatch = true;
                        break;
                    default : // more than one
                        $message = 'Multiple matches on UID for Ilios user in external user store';
                        $message .= " (Ilios user id: {$nonStudent['user_id']}, UID: {$nonStudent['uc_uid']})";
                        throw new Ilios_UserSync_Process_ExternalUserException($message);
                }
                if ($userMatch) {
                    $extUser = $extUsers->current();

                    // validate external user
                    $this->_validateIncomingUserRecord($extUser);

                    // get matching user from ilios by Uid, excluding disabled and flagged-to-be-ignored records
                    $iliosUsers = $this->_userDao->getNonStudentUsersWithEmail($extUser->getEmail(), true, true);

                    // -----------------------------
                    // 3a. update user(s)
                    // -----------------------------
                    $updated = $this->_processMatchingUsers($processId, $logger, $extUser, $nonStudent, $iliosUsers, self::MATCH_ILIOS_USERS_BY_EMAIL);
                    if ($updated) {
                        $updatedUsersCount++;
                    }
                } else {
                    // --------------------------------------------------
                    // 2b. match by email (this is a fallback)
                    // --------------------------------------------------
                    $extUsers = $this->_userSource->getUserByEmail($nonStudent['email']);
                    $userMatch = false; // reset the flag, check again
                    $extUsersCount = count($extUsers);
                    switch ($extUsersCount) {
                        case 0 :
                            // no match
                            // do nothing here
                            break;
                        case 1 :
                            // exactly one match
                            // set $userMatch flag
                            $userMatch = true;
                            break;
                        default : // more than one
                            $message = 'Multiple matches on Email for Ilios user in external user store';
                            $message .= " (Ilios user id: {$nonStudent['user_id']}, Email: {$nonStudent['email']})";
                            throw new Ilios_UserSync_Process_ExternalUserException($message);
                    }
                    if ($userMatch) {
                        $extUser = $extUsers->current();

                        // validate external user
                        $this->_validateIncomingUserRecord($extUser);

                        //  get matching user from ilios by Uid, excluding disabled and flagged-to-be-ignored records
                        $iliosUsers = $this->_userDao->getNonStudentUsersWithUid($extUser->getUid(), true, true);

                        // -----------------------------
                        // 3b. update user(s)
                        // -----------------------------
                        $this->_processMatchingUsers($processId, $logger, $extUser, $nonStudent, $iliosUsers, self::MATCH_ILIOS_USERS_BY_UID);
                        if ($updated) {
                            $updatedUsersCount++;
                        }
                    } else {
                        // ----------------------------------------------
                        // 2c. no matches
                        // ----------------------------------------------

                        // the assumption here is that if there is a
                        // (non-student) record in Ilios with NO referent in
                        // the external user store, they are there on purpose
                        // or record in the ext. user store is on the way,
                        // and the Ilios record is the primary source at the time
                        // of comparison.
                        continue;
                    }
                }
            } catch  (Ilios_UserSync_Process_ExternalUserException $e)  {
                // catch external user validation exceptions here.
                // log them and move on to the next record.
                $logger->log($e->getMessage(), $processId, 1, Ilios_Logger::LOG_LEVEL_ERROR);
            } catch (Ilios_UserSync_Exception $e) {
                // Catch any other exception thrown by the process
                // this should only occur if something within the process itself
                // blew up, e.g. the external user store went down or the likes.
                // We log the incident and abort mission here.
                $logger->log($e->getMessage(), $processId, 0, Ilios_Logger::LOG_LEVEL_ERROR);
                $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
                $msg = "Aborting non-student sync process prematurely.";
                $logger->log($msg, $processId);
                $msg  = "# of non-students processed: {$processedUsersCount}";
                $logger->log($msg, $processId);
                $msg = "# of non-students updated: {$updatedUsersCount}";
                $logger->log($msg, $processId);
                $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
                return false;
            }
        }

        // ----------------------------------
        // 3. Post-run Processing
        // ----------------------------------
        $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
        $msg  = "Completed non-student sync process.";
        $logger->log($msg, $processId);
        $msg  = "# of non-students processed: {$processedUsersCount}";
        $logger->log($msg, $processId);
        $msg = "# of non-students updated: {$updatedUsersCount}";
        $logger->log($msg, $processId);
        $logger->log(Ilios_Logger::LOG_SEPARATION_LINE, $processId);
        return true;
    }

    /**
     * Implements business logic for dealing with zero, one or multiple matches
     * between a given external user and Ilios user records.
     * Depending on the number of matches,
     * - nothing happens (conditions: zero matches)
     * - an Ilios user record gets updated with the properties of the external user (exactly one match)
     * - Ilios user records get disabled and flagged for manual review (multiple matches/"duplicates")
     * @param int $processId
     * @param Ilios_Logger $logger
     * @param Ilios_UserSync_ExternalUser $extUser an external user record
     * @param array $nonStudent a non-student ilios record that was used to identify the external user record
     * @param array $iliosUsers a nested array of Ilios user record(s) identified by matching
     * @param string $matchCriterium indicates what criterium was used to match the given ext. user against Ilios users (default is UID)
     * @return boolean TRUE if user record got updated, FALSE otherwise.
     */
    protected function _processMatchingUsers ($processId, Ilios_Logger $logger, Ilios_UserSync_ExternalUser $extUser,
        array $nonStudent, array $iliosUsers, $matchCriterium = self::MATCH_ILIOS_USERS_BY_UID)
    {
        // tally up the matching user and proceed accordingly
        $userCount = count($iliosUsers);
        try {
            switch ($userCount) {
                case 0 :
                    // no matching enabled users were found.
                    // this also means that the current user was not found.
                    // to be matching the external user record under the given criterium
                    // In other words, we got a partial match.
                    // Flag the current non-student user under inspection
                    // for manual review.
                    switch ($matchCriterium) {
                        case self::MATCH_ILIOS_USERS_BY_EMAIL :
                            $message = 'Mismatch Error: External user record matched Ilios user on UID, but not on email.';
                            $message .= " (Ilios user id : {$nonStudent['user_id']}, ";
                            $message .= " matching UID: {$extUser->getUid()}, email mismatch: {$extUser->getEmail()}/{$nonStudent['email']})";
                            throw new Ilios_UserSync_Process_UserException($message,
                                        Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_EMAIL_MISMATCH, 'email', $extUser->getEmail());
                            break;
                        case self::MATCH_ILIOS_USERS_BY_UID :
                        default :
                            $message = 'Mismatch Error: External user record matched Ilios user on email, but not on UID.';
                            $message .= " (Ilios user id : {$nonStudent['user_id']}, ";
                            $message .= " matching email: {$extUser->getEmail()}, UID mismatch: {$extUser->getUid()}/{$nonStudent['uc_uid']})";
                            throw new Ilios_UserSync_Process_UserException($message,
                                        Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_UID_MISMATCH, 'uid', $extUser->getUid());

                    }
                case 1 :
                    // exactly one user was found - update that matching record
                    // double-check that this is indeed the same non-student
                    // user we started out with.
                    $iliosUser = $iliosUsers[0];
                    if ($iliosUser['user_id'] == $nonStudent['user_id']) {
                        $this->_updateUser($extUser, $iliosUser);
                        // log the update
                        $msg = "Updated user. (Ilios user id: {$nonStudent['user_id']}, UID: {$nonStudent['uc_uid']})";
                        $logger->log($msg, $processId, 1);
                        return true;
                    } else {
                        // EDGE CASE:
                        // this means that an external user record
                        // partially matches multiple Ilios user records,
                        // but on different attributes.
                        // e.g. it may match one ilios user on email,
                        // and a different one on uid.
                        // in this case, we flag the current (partially matching) Ilios user
                        // for review and move on.
                        switch ($matchCriterium) {
                            case self::MATCH_ILIOS_USERS_BY_EMAIL :
                                $message = "Mismatch error: External user record matches Ilios user on UID ";
                                $message .= ", but a different Ilios user record on email.";
                                $message .=" (Ilios user id : {$nonStudent['user_id']}, ";
                                $message .= " matching UID: {$extUser->getUid()}, email mismatch: {$extUser->getEmail()}/{$nonStudent['email']})";
                                break;
                            case self::MATCH_ILIOS_USERS_BY_UID :
                            default :
                                $message = "Mismatch error: External user record matches Ilios user on email, ";
                                $message .= ", but a different Ilios user record on UID. ";
                                $message .=" (Ilios user id : {$nonStudent['user_id']}, ";
                                $message .= " matching email: {$extUser->getEmail()}, UID mismatch: {$extUser->getUid()}/{$nonStudent['uc_uid']})";
                        }
                        throw new Ilios_UserSync_Process_UserException($message,
                                    Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_PARTIAL_MISMATCH);
                    }
                    break;
                default :
                    // multiple matches
                    // throw a user sync exception and deal with it further downstream
                    $msg = "Mismatch error: multiple ($userCount) enabled users found that match an external user";
                    switch ($matchCriterium) {
                        case self::MATCH_ILIOS_USERS_BY_EMAIL :
                            $email = $extUser->getEmail();
                            $msg .= " by email (email = {$email})";
                            break;
                        case self::MATCH_ILIOS_USERS_BY_UID :
                        default :
                            $uid = $extUser->getUid();
                            $msg .= " by UID (uid = {$uid})";
                    }
                    throw new Ilios_UserSync_Process_UserException($msg,
                                Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_USER_DUPLICATES);

                    }
        } catch (Ilios_UserSync_Process_UserException $e) {
            $this->_saveUserSyncException($processId, $nonStudent['user_id'], $e);
            $logger->log($e->getMessage(), $processId, 1, Ilios_Logger::LOG_LEVEL_WARN);
        }
        return false;
    }

    /**
     * Update a given user's properties with the properties
     * of a given corresponding external user record.
     * Then flag this user as 'examined'.
     * @param Ilios_UserSync_ExternalUser $externalUser
     * @param array $user the existing ilios user record
     */
    protected function _updateUser (Ilios_UserSync_ExternalUser $externalUser, array $user)
    {
        // update user record
        $this->_userDao->updateUser($user['user_id'],
             $externalUser->getFirstName(),
             $externalUser->getMiddleName(),
             $externalUser->getLastName(),
             true, // this does not matter
             $externalUser->getPhone(),
             false // ACHTUNG! we do not want to touch up already established user role associations!
             );
    }

    /**
     * Validates a given external user record.
     * Throws an exception if validations fails.
     * @throws Ilios_UserSync_Process_ExternalUserException
     */
    protected function _validateIncomingUserRecord (Ilios_UserSync_ExternalUser $user)
    {
        $uid = $user->getUid();
        if (empty($uid)) {
            throw new Ilios_UserSync_Process_ExternalUserException(
            			'Missing UID for inbound user: ' . $user,
                        Ilios_UserSync_Process_ExternalUserException::INVALID_UID);
        }
        $email = $user->getEmail();
        if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Ilios_UserSync_Process_ExternalUserException(
            			'Missing or invalid email for inbound user: ' . $user,
                        Ilios_UserSync_Process_ExternalUserException::INVALID_EMAIL);
        }
    }

    /**
     * Retrieves all enabled non-student users from Ilios,
     * excluding users flagged to be ignored during the user sync as well.
     * @return array nested array of users
     */
    protected function _getEnabledNonStudents ()
    {
        return $this->_userDao->getNonStudentUsers(true, true);
    }
}
