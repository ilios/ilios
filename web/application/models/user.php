<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "user" table.
 */
class User extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('user', array('user_id'));
        $this->load->model('School', 'school', true);
        $this->load->model('User_Role', 'roles', true);
    }

    /**
     * Creates a new user record and assigns the "faculty" role to it.
     * @param string $lastName
     * @param string $firstName
     * @param string $middleName
     * @param string $phone
     * @param string $email
     * @param string $ucUID
     * @param string $otherId
     * @param int $primarySchoolId
     * @param array|NULL $auditAtoms
     * @return int the newly created user id
     * @see User::addUser()
     */
    public function addUserAsFaculty ($lastName, $firstName, $middleName, $phone, $email, $ucUID,
        $otherId, $primarySchoolId, &$auditAtoms = null)
    {
        return $this->addUser($lastName, $firstName, $middleName, $phone, $email, $ucUID,
            $otherId, $primarySchoolId, null, User_Role::FACULTY_ROLE_ID, $auditAtoms);
    }

    /**
     * Creates a new user record and assigns the "student" role to it.
     * @param string $lastName
     * @param string $firstName
     * @param string $middleName
     * @param string $phone
     * @param string $email
     * @param string $ucUID
     * @param string $otherId
     * @param int|NULL $primaryCohortId
     * @param int $primarySchoolId
     * @param array|NULL $auditAtoms
     * @return int the newly created user id
     * @see User::addUser()
     */
    public function addUserAsStudent ($lastName, $firstName, $middleName, $phone, $email, $ucUID,
        $otherId, $primaryCohortId, $primarySchoolId, &$auditAtoms = null)
    {
        return $this->addUser($lastName, $firstName, $middleName, $phone, $email, $ucUID,
            $otherId, $primarySchoolId, $primaryCohortId, User_Role::STUDENT_ROLE_ID, $auditAtoms);
    }

    /**
     * Creates a new user and (if provided):
     * - assigns a given user role to it
     * - assigns the user to a given primary cohort
     * @param string $lastName
     * @param string $firstName
     * @param string $middleName
     * @param string $phone
     * @param string $email
     * @param string $ucUID
     * @param string $otherId
     * @param int $primarySchoolId
     * @param int|NULL $primaryCohortId
     * @param int|NULL $primaryRoleId
     * @param array|NULL $auditAtoms
     * @return int the newly created user id
     */
    public function addUser ($lastName, $firstName, $middleName, $phone, $email, $ucUID,
        $otherId, $primarySchoolId, $primaryCohortId = null, $primaryRoleId = null, &$auditAtoms = null)
    {
        $newRow = array();
        $newRow['user_id'] = null;

        $newRow['last_name'] = $lastName;
        $newRow['first_name'] = $firstName;
        $newRow['middle_name'] = $middleName;
        $newRow['phone'] = $phone;
        $newRow['email'] = $email;
        $newRow['primary_school_id'] = $primarySchoolId;
        $newRow['added_via_ilios'] = '1';   // todo this is true here i guess.. maybe.. maybe not
        $newRow['enabled'] = '1';
        $newRow['uc_uid'] = $ucUID;
        $newRow['other_id'] = $otherId;

        $this->db->insert($this->databaseTableName, $newRow);

        $newId = $this->db->insert_id();

        if (is_array($auditAtoms)) {
            $auditAtoms[] = $this->auditEvent->wrapAtom($newId, 'user_id', $this->databaseTableName,
                Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1);
        }

        //  assign primary role, if applicable
        if ($primaryRoleId) {
            $newRow = array();
            $newRow['user_id'] = $newId;
            $newRow['user_role_id'] = $primaryRoleId;
            $this->db->insert('user_x_user_role', $newRow);

            if (is_array($auditAtoms)) {
                $auditAtoms[] = $this->auditEvent->wrapAtom($newId, 'user_id', 'user_x_user_role',
                   Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
            }
        }

        // assign new user to given cohort, if applicable
        if ($primaryCohortId) {
            $this->_addUserToCohort($newId, $primaryCohortId, true);
        }

        return $newId;
    }

    /**
     * Deletes all recorded sync exceptions for a given user.
     * @param int $userId The user id.
     */
    public function deleteSyncExceptionsForUser ($userId)
    {
        if (empty($userId)) {
            return;
        }
        $this->db->where('user_id', $userId);
        $this->db->delete('user_sync_exception');
    }

    /**
     * Returns the total of students without primary cohort association in a given school.
     * Please note the following implicit constraints that apply:
     * @param int $schoolId
     * - disabled user accounts are excluded
     * @return int the number of cohortless students
     */
    public function getCountForStudentsWithoutPrimaryCohort ($schoolId = null)
    {
        $queryResults = $this->getStudentsWithoutPrimaryCohort($schoolId, true);
        return $queryResults->num_rows();
    }

    /**
     * Retrieves a list of non-flagged and enabled users with user sync exceptions.
     * @param int $schoolId if given then the users are filtered by primary school affiliation.
     * @return array an nested array of arrays, containing user info including sync exceptions and roles
     */
    public function getUsersWithSyncExceptions ($schoolId = -1)
    {
        $rhett = array();
        $clean = array();

        // retrieve data of users with sync exceptions
        // this includes associated user roles and sync exceptions for these users
        $sql =<<<EOL
SELECT
`user`.`user_id`,
`user`.`first_name`,
`user`.`last_name`,
`user`.`middle_name`,
`user`.`uc_uid`,
`user`.`email`,
`user`.`phone`,
`user`.`primary_school_id`,
`user`.`enabled`,
`user`.`user_sync_ignore`,
`user`.`primary_school_id`,
`user_sync_exception`.`exception_id`,
`user_sync_exception`.`exception_code`,
`user_sync_exception`.`mismatched_property_name`,
`user_sync_exception`.`mismatched_property_value`,
`user_x_user_role`.`user_role_id`,
`user_x_cohort`.`cohort_id`
FROM `user`
JOIN `user_sync_exception`
ON `user_sync_exception`.`user_id` = `user`.`user_id`
LEFT JOIN `user_x_user_role`
ON `user_x_user_role`.`user_id` = `user`.`user_id`
LEFT JOIN `user_x_cohort`
ON `user_x_cohort`.`user_id` = `user`.`user_id` AND `user_x_cohort`.`is_primary` = 1
WHERE `user`.`enabled` = 1
AND `user`.`user_sync_ignore` = 0
EOL;
        if (0 < $schoolId) {
            $clean['school_id'] = (int) $schoolId;
            $sql .= " AND `user`.`primary_school_id` = {$clean['school_id']}";
        }
        $sql .= ' ORDER BY `user`.`user_id`';

        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Updates a given user with given attributes.
     * @param int $userId the user record id
     * @param string $firstName
     * @param string $middleInitial
     * @param string $lastName
     * @param boolean $isStudent
     * @param string $phone
     * @param boolean $affectUserRoles if set to TRUE then the user's role associations are updated as well.
     */
    public function updateUser ($userId, $firstName, $middleInitial, $lastName, $isStudent, $phone, $affectUserRoles = true)
    {
        $updateRow = array();

        $updateRow['first_name'] = $firstName;
        $updateRow['middle_name'] = $middleInitial;
        $updateRow['last_name'] = $lastName;
        $updateRow['phone'] = $phone;

        $this->db->where('user_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);

        if ($affectUserRoles) {
            $this->affectRoleForUser($userId, ($isStudent ? User_Role::STUDENT_ROLE_ID : User_Role::FACULTY_ROLE_ID), true);
        }
    }

    /**
     * Establishes or removes a given user/user role association.
     * @param int $userId
     * @param int $userRoleId
     * @param boolean $addRole if set to TRUE then the given role association is added, if set to FALSE it will be removed.
     */
    public function affectRoleForUser ($userId, $userRoleId, $addRole)
    {
        $roleExists = $this->userHasRole($userId, $userRoleId);

        if ($addRole) {
            if (! $roleExists) {
                $newRow = array();

                $newRow['user_id'] = $userId;
                $newRow['user_role_id'] = $userRoleId;

                $this->db->insert('user_x_user_role', $newRow);
            }
        }
        else {
            if ($roleExists) {
                $this->db->where('user_id', $userId);
                $this->db->where('user_role_id', $userRoleId);
                $this->db->delete('user_x_user_role');
            }
        }
    }

    /**
     * Retrieves the login handle and associated roles of a given user.
     * @param int $userId the user id
     * @return array and assoc array containing
     *     'ilios_auth_username' .. the user login name, or FALSE if not specified
     *     'roles' ... an array of associate arrays, each representing a user role record
     */
    public function getAttributesForUser ($userId)
    {
        $rhett = array();
        $rhett['ilios_auth_username'] = false; // default
        $clean = array();
        $clean['user_id'] = (int) $userId;

        $queryString = "SELECT username FROM authentication WHERE person_id = {$clean['user_id']}";
        $queryResults = $this->db->query($queryString);
        if ($queryResults->num_rows() == 1) {
            $authRow = $queryResults->first_row();
            $rhett['ilios_auth_username'] = $authRow->username;
        }

        $rhett['roles'] = $this->_getRoleArray($userId);

        return $rhett;
    }

    /**
     * Retrieves the ids of all users that have a given user-role assigned to them.
     *
     * @param int $roleId The user-role id.
     * @return array An array of user ids.
     */
    public function getUsersWithRoleId ($roleId)
    {
        $rhett = array();

        $clean = array();
        $clean['role_id'] = (int) $roleId;

        $sql =<<< EOL
SELECT u.`user_id`
FROM `user` u
JOIN `user_x_user_role` uxur ON uxur.`user_id` = u.`user_id`
WHERE uxur.`user_role_id` = {$clean['role_id']}
EOL;

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $rhett[] = $row['user_id'];
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * Retrieves enabled ("active") user-accounts that match a given email address
     * @param string $emailAddress
     * @return array
     */
    public function getEnabledUsersWithEmailAddress ($emailAddress)
    {
        $rhett = array();

        $this->db->where('email', $emailAddress);
        $this->db->where('enabled', 1);

        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * Searches users by name/name fragment and given account status.
     * @param string $name the user name/name-fragment
     * @param boolean $includeDisabled if set to TRUE then disabled accounts will be included.
     * @return CI_DB_result a db query result object
     */
    public function getUsersFilteredOnName ($name, $includeDisabled = false)
    {
        $clean = array();
        $clean['name'] = $this->db->escape_like_str($name);

        $this->db->from('user');

        if (! $includeDisabled) {
            $this->db->where('enabled', '1');
        }

        $len = strlen($name);

        if (! $name) {
            // search all
        } elseif (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
            // trailing wildcard search
            $this->db->where("(`last_name` LIKE '{$clean['name']}%' OR `first_name` LIKE '{$clean['name']}%' OR `middle_name` LIKE '{$clean['name']}%')");
        } else {
            // full wildcard search
            $this->db->where("(`last_name` LIKE '%{$clean['name']}%' OR `first_name` LIKE '%{$clean['name']}%' OR `middle_name` LIKE '%{$clean['name']}%')");
        }

        $this->db->order_by('last_name', 'asc');
        $this->db->order_by('first_name', 'asc');
        $this->db->order_by('middle_name', 'asc');

        return $this->db->get();
    }

    /**
     * Retrieves a list of users associated with a given cohort and,
     * optionally, filtered down by primary school association and user-account status.
     * @param int $cohortId the cohort id
     * @param int $schoolId (optional) if given then only users with the same primary school
     *    will be included. The default is to ignore primary school affiliations.
     * @param boolean $enabledOnly (optional) If set to TRUE then only enabled user accounts
     *    will be included. the default is FALSE.
     * @return CI_DB_result the query-results object
     */
    public function getUsersForCohort ($cohortId, $schoolId = null, $enabledOnly = false)
    {
        $this->db->join('user_x_cohort', 'user_x_cohort.user_id = user.user_id');
        $this->db->where('user_x_cohort.cohort_id', $cohortId);
        if (! is_null($schoolId)) {
            $this->db->where('user.primary_school_id', $schoolId);
        }

        if ($enabledOnly) {
            $this->db->where('user.enabled', 1);
        }
        $this->db->order_by('user.last_name');
        $this->db->select('user.*');
        return $this->db->get('user');
    }

    /**
     * Retrieves a list of "student" users without primary cohort association, optionally filtered down
     * by a given primary school and user-account status.
     * @param int $schoolId (optional) if given then only users with the same primary school
     *    will be included. The default is to ignore primary school affiliations.
     * @param boolean $enabledOnly (optional) If set to TRUE then only enabled user accounts
     *    will be included. the default is FALSE.
     * @return CI_DB_result the query-results object
     */
    public function getStudentsWithoutPrimaryCohort ($schoolId = null, $enabledOnly = false)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['role_id'] = User_Role::STUDENT_ROLE_ID;
        $sql =<<< EOL
SELECT `user`.*
FROM `user`
JOIN `user_x_user_role` ON `user`.`user_id` = `user_x_user_role`.`user_id`
WHERE
`user_x_user_role`.`user_role_id` = {$clean['role_id']}
AND `user`.`user_id` NOT IN (
  SELECT `user_id` FROM `user_x_cohort`
  WHERE `user_x_cohort`.`is_primary` = 1
)
EOL;
        $where = array();
        if (! is_null($schoolId)) {
            $sql .= " AND `user`.`primary_school_id` = {$clean['school_id']}";
        }

        if ($enabledOnly) {
            $sql .= ' AND `user`.`enabled` = 1';
        }

        $sql .= " ORDER BY `user`.`last_name`";

        return $this->db->query($sql);
    }

    /*
     * @return users in a non-associative array
     */
    public function getUsersForCohortAsArray ($cohortId)
    {
        $userArray = array();

        $queryResults = $this->getUsersForCohort($cohortId);
        foreach ($queryResults->result_array() as $row) {
            array_push($userArray, $row);
        }

        return $userArray;
    }

    public function getUsersWithCohortIdFilteredOnName ($name, $cohortId, $reverseOrdering = false)
    {
        $this->db->like('user.last_name', $name, 'both');
        $this->db->or_like('user.first_name', $name, 'both');
        $this->db->or_like('user.middle_name', $name, 'both');
        $this->db->join('user_x_cohort', 'user_x_cohort.user_id = user.user_id');
        $this->db->where('user_x_cohort.cohort_id', $cohortId);

        if ($reverseOrdering) {
            $this->db->order_by('user.last_name', 'desc');
        }
        else {
            $this->db->order_by('user.last_name');
        }
        $this->db->select("user.*");
        return $this->db->get('user');
    }

    /**
     * Retrieves a list of (enabled) users who have been flagged as 'not examined'.
     * NOTE: disabled user accounts are implictly excluded from this list.
     * @param boolean $studentsOnly if set to TRUE then filter out all non-student user records
     * @param boolean $excludeIgnored if set to TRUE then filter out all user records who are set to be ignored during user synchronization.
     * @return array a nested array of arrays of user records
     */
    public function getUnexaminedUsers ($studentsOnly = false, $excludeIgnored = false)
    {
        $clean = array();
        $clean['student_role'] = User_Role::STUDENT_ROLE_ID;
        // build the query string
        $sql =<<< EOL
SELECT
`user`.`user_id`,
`user`.`last_name`,
`user`.`first_name`,
`user`.`middle_name`,
`user`.`phone`,
`user`.`email`,
`user`.`primary_school_id`,
`user`.`added_via_ilios`,
`user`.`enabled`,
`user`.`uc_uid`,
`user`.`other_id`,
`user`.`examined`,
`user`.`user_sync_ignore`
FROM `user`
EOL;
        if ($studentsOnly) { // filter out non-students
            $sql .=<<< EOL

JOIN `user_x_user_role` ON `user`.`user_id` = `user_x_user_role`.`user_id`
WHERE
`user_x_user_role`.`user_role_id` = {$clean['student_role']}
AND `user`.`examined` = 0
AND `user`.`enabled` = 1
EOL;
        } else {
            $sql .=<<< EOL

WHERE
`user`.`examined` = 0
AND `user`.`enabled` = 1
EOL;
        }
        if ($excludeIgnored) { // filter out users set to be ignored
            $sql .=<<< EOL

AND `user`.`user_sync_ignore` = 0
EOL;
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Resets the 'examined' flag on user records.
     * @param boolean $studentsOnly if TRUE then only reset student records.
     */
    public function clearUsersExaminedBit ($studentsOnly = false)
    {
        $clean = array();
        $clean['student_role'] = User_Role::STUDENT_ROLE_ID;
        $query = 'UPDATE `user` SET `examined` = 0';
        if ($studentsOnly) {

            $query =<<< EOL
UPDATE `user`
SET `examined` = 0
WHERE `user_id` IN (
  SELECT `user_id` FROM
  `user_x_user_role` WHERE `user_role_id` = {$clean['student_role']}
)
EOL;
        }
        $this->db->query($query);
    }

    /**
     * Enables or disables a given user.
     * @param int $userId The user id.
     * @param boolean If set to TRUE then the user account will be enabled, if set to FALSE it will be disabled.
     */
    public function enableUser ($userId, $enable)
    {
        $updateRow = array();
        $updateRow['enabled'] = $enable ? 1 : 0;

        $this->db->where('user_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);
    }

    /**
     * Flags a given user as examined or unexamined.
     * @param int $userId The user id.
     * @param boolean $examined If set to TRUE then the user account will be flagged as examined,
     *  if set to FALSE it will be flagged as unexamined.
     */
    public function setUserExaminedBit ($userId, $examined)
    {
        $updateRow = array();
        $updateRow['examined'] = $examined ? 1 : 0;

        $this->db->where('user_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);
    }

    /**
     * Toggles the sync ignore flag for a given user on or off.
     * @param int $userId The user id.
     * @param boolean $ignore Set to TRUE to turn the ignore-flag on, FALSE to turn it off.
     */
    public function setSyncIgnoreBit ($userId, $ignore)
    {
        $updateRow['user_sync_ignore'] = $ignore ? 1 : 0;
        $this->db->where('user_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);
    }

    /**
     * Assigns a primary cohort to a given user.
     * @param int $userId The user id.
     * @param int $cohortId The cohort id.
     */
    public function assignPrimaryCohort ($userId, $cohortId)
    {
        // create record if not exists
        if (false === $this->_getUserCohort($userId, $cohortId)) {
            $this->_addUserToCohort($userId, $cohortId, true);
        } else { // flags an existing user/cohort association as the primary one
            $this->_setPrimaryCohort($userId, $cohortId);
        }
    }

    public function changeEmailAddress ($userId, $newAddress, $assureSystemWideUniqueness = false)
    {
        $updateRow = array();
        $updateRow['email'] = $newAddress;

        $this->db->where('user_id', $userId);
        $this->db->update($this->databaseTableName, $updateRow);

        $success = ($this->db->affected_rows() == 1);

        if ($success && $assureSystemWideUniqueness) {
            $updateRow = array();
            $updateRow['email'] = 'invalid@addre.ss';

            $this->db->where('email', $newAddress);
            $this->db->where('user_id !=', $userId);
            $this->db->update($this->databaseTableName, $updateRow);
        }

        return $success;
    }

    /**
     * Checks if an enabled user account with a given email address exists.
     * @param String $email the user's email address
     * @return boolean TRUE if the user exists, FALSE otherwise.
     */
    public function userExistsWithEmail ($email)
    {
        $this->db->where('email', $email);
        $this->db->where('enabled', 1);

        $query = $this->db->get($this->databaseTableName);
        $rhett = ($query->num_rows() > 0);
        $query->free_result();
        return $rhett;
    }

    /**
     * Checks whether a given user has a been assigned to a  given user-role.
     * @param int $userId the user id
     * @param int $roleId the user-role id
     * @return boolean TRUE on success, FALSE otherwise.
     */
    public function userHasRole ($userId, $roleId)
    {
        $this->db->where('user_id', $userId);
        $this->db->where('user_role_id', $roleId);
        $query = $this->db->get('user_x_user_role');
        $rhett = ($query->num_rows() > 0);
        $query->free_result();
        return $rhett;
    }

    /**
     * Checks if a given user is assigned to at least one
     * from a list of given user-roles.
     * @param int $userId the user id
     * @param array $roleIds a list of user-role ids.
     * @return boolean TRUE on success, FALSE otherwise.
     */
    public function userInRoles ($userId, array $roleIds = array())
    {
        $this->db->where('user_id', $userId);
        $this->db->where_in('user_role_id', $roleIds);
        $query = $this->db->get('user_x_user_role');
        $rhett = ($query->num_rows() > 0);
        $query->free_result();
        return $rhett;
    }

    /**
     * Checks if a given user account has been disabled.
     * @param int $userId the user id
     * @return boolean TRUE if the user account is disabled, FALSE if the user account is enabled.
     */
    public function userAccountIsDisabled ($userId)
    {
        $aRow = $this->getRowForPrimaryKeyId($userId);

        return ($aRow->enabled == 0);
    }

    /**
     * Checks if a given user is associated has "instructor"-level access rights in the system.
     * For this to be true, the user must be associated with at least one of the
     * 'Developer', 'Faculty' or 'Course Director' roles.
     * @param int $userId the user id
     * @return boolean TRUE if user has sufficient access rights, FALSE if not.
     */
    public function userHasInstructorAccess ($userId)
    {
        $roles = array(User_Role::COURSE_DIRECTOR_ROLE_ID, User_Role::DEVELOPER_ROLE_ID, User_Role::FACULTY_ROLE_ID);
        return $this->userInRoles($userId, $roles);
    }

    /**
     * Checks if a given user is associated has "admin"-level access rights in the system.
     * For this to be true, the user must be associated with at least one of the
     * 'Developer' and 'Course Director' roles.
     * @param int $userId the user id
     * @return boolean TRUE if user has sufficient access rights, FALSE if not.
     */
    public function userHasAdminAccess ($userId)
    {
        $roles = array(User_Role::COURSE_DIRECTOR_ROLE_ID, User_Role::DEVELOPER_ROLE_ID);
        return $this->userInRoles($userId, $roles);
    }

    /**
     * Alias for <code>User_Role::userIsStudent()</code>
     * @param int $userId
     * @return boolean
     * @see User::userIsStudent()
     */
    public function userIsLearner ($userId)
    {
        return $this->userIsStudent($userId);
    }

    /**
     * Checks if a given user has the 'student' role assigned.
     * @param int $userId
     * @return boolean TRUE if the user has the 'student' role, otherwise FALSE
     */
    public function userIsStudent ($userId)
    {
        return $this->userHasRole($userId, User_Role::STUDENT_ROLE_ID);
    }

    /**
     * Checks if a given user has the 'course director' role assigned.
     * @param int $userId
     * @return boolean TRUE if the user has the 'course director' role, otherwise FALSE
     */
    public function userIsCourseDirector ($userId)
    {
        return $this->userHasRole($userId, User_Role::COURSE_DIRECTOR_ROLE_ID);
    }

    /**
     * Returns the formatted user name for a given user.
     * @param int $userId the user id
     * @param boolean $lastNameFirst set to TRUE to print the last name first
     * @param boolean $firstInitialOnly set to TRUE to print the first name initial instead of the full first name
     * @return string|boolean the formatted user name, or FALSE if the user could not be found
     */
    public function getFormattedUserName ($userId, $lastNameFirst, $firstInitialOnly = false)
    {
        $row = $this->getRowForPrimaryKeyId($userId);

        $rhett = '';

        if ($lastNameFirst) {
            $rhett = $row->last_name . ', ';

            if ($firstInitialOnly) {
                $rhett .= substr($row->first_name, 0, 1) . '.';
            }
            else {
                $rhett .= $row->first_name;
            }
        }
        else {
            if ($firstInitialOnly) {
                $rhett = substr($row->first_name, 0, 1) . '.';
            }
            else {
                $rhett = $row->first_name;
            }

            $rhett .= ' ' . $row->last_name;
        }
        return $rhett;
    }

    /**
     * Performs a name search for enabled users who have been assigned a given role.
     * @param string $name the user name/name-fragment
     * @param string $roleTitle should be a valid user_role.title value in the database
     * @return CI_DB_result a db query result object
     */
    public function getUsersFilteredOnNameMatchWithRoleTitle ($name, $roleTitle)
    {
        $clean = array();
        $clean['name'] = $this->db->escape_like_str($name);

        $this->db->select('user.user_id');
        $this->db->select('user.last_name');
        $this->db->select('user.first_name');
        $this->db->select('user.middle_name');
        $this->db->select('user.phone');
        $this->db->select('user.email');
        $this->db->select('user.uc_uid');
        $this->db->select('user.other_id');

        $this->db->from('user');
        $this->db->join('user_x_user_role', 'user.user_id = user_x_user_role.user_id');
        $this->db->join('user_role', 'user_x_user_role.user_role_id = user_role.user_role_id');


        $this->db->where('user_role.title', $roleTitle);
        $this->db->where('user.enabled', 1);

        $len = strlen($name);
        if ('' === trim($name)) {
            // search all
        } elseif (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
            // trailing wildcard search
            $this->db->where("(`user`.`last_name` LIKE '{$clean['name']}%'"
            . " OR `user`.`first_name` LIKE '{$clean['name']}%'"
            . " OR `user`.`middle_name` LIKE '{$clean['name']}%')");
        } else {
            // full wildcard search
            $this->db->where("(`user`.`last_name` LIKE '%{$clean['name']}%'"
            . " OR `user`.`first_name` LIKE '%{$clean['name']}%'"
            . " OR `user`.`middle_name` LIKE '%{$clean['name']}%')");
        }

        $this->db->order_by('user.last_name', 'asc');
        $this->db->order_by('user.first_name', 'asc');
        $this->db->order_by('user.middle_name', 'asc');

        $this->db->distinct();

        return $this->db->get();
    }

    /**
     * Checks if at least one user with a given UID exists.
     * @param int $uid
     * @param boolean $enabledOnly
     * @param boolean $excludeIgnored
     * @return boolean TRUE if at least one user exists, FALSE otherwise.
     */
    public function hasUsersWithUid ($uid, $enabledOnly = false, $excludeIgnored = false)
    {
        $rhett = false;
        $clean = array();
        $clean['uid'] = $this->db->escape($uid);

        $sql =<<< EOL
SELECT COUNT(`user`.`user_id`) AS 'c'
FROM `user`
WHERE `user`.`uc_uid` = {$clean['uid']}
EOL;
        if ($enabledOnly) {
            $sql .= ' AND `user`.`enabled` = 1';
        }
        if ($excludeIgnored) {
            $sql .= ' AND `user`.`user_sync_ignore` = 0';
        }
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $rhett = 0 < (int) $row['c'] ? true : false; // check the count
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all enabled user records with the same given UID.
     * @param string $uid
     * @param boolean $enabledOnly
     * @param boolean $excludeIgnored
     * @return array a nested array of user records
     */
    public function getUsersWithUid ($uid, $enabledOnly = false, $excludeIgnored = false)
    {
        $rhett = array();

        $this->db->where('uc_uid', $uid);
        if ($enabledOnly) {
            $this->db->where('enabled', '1');
        }
        if ($excludeIgnored) {
            $this->db->where('user_sync_ignore', '0');
        }
        $query  = $this->db->get($this->databaseTableName);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all enabled user records with the given school id.
     * @param int $schoolId
     * @return array a nested array of user records
     */
    public function getUsersWithPrimarySchoolId ($schoolId)
    {
        $rhett = array();

        $this->db->where('primary_school_id', $schoolId);
        $this->db->where('enabled', '1');

        $query = $this->db->get($this->getTableName());
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all non-student users matching a given UID.
     * @param string $uid
     * @param boolean $enabledOnly if TRUE then filter out disabled users
     * @param boolean $excludeIgnored if TRUE then filter out users who are flagged to be 'ignored' during the automated user sync process.
     * @return array a nested array of user records
     */
    public function getNonStudentUsersWithUid ($uid, $enabledOnly = false, $excludeIgnored = false)
    {
        $rhett = array();
        $clean = array();
        $clean['uid'] = $this->db->escape($uid);
        $clean['student_role'] = User_Role::STUDENT_ROLE_ID;
        $sql =<<< EOL
SELECT DISTINCT
`user`.`user_id`,
`user`.`last_name`,
`user`.`first_name`,
`user`.`middle_name`,
`user`.`phone`,
`user`.`email`,
`user`.`primary_school_id`,
`user`.`added_via_ilios`,
`user`.`enabled`,
`user`.`uc_uid`,
`user`.`other_id`,
`user`.`examined`,
`user`.`user_sync_ignore`
FROM `user`
JOIN `user_x_user_role`
ON `user_x_user_role`.`user_id` = `user`.`user_id`
WHERE
`user`.`uc_uid` = {$clean['uid']}
AND NOT EXISTS (
  SELECT `user_x_user_role`.`user_id`
  FROM `user_x_user_role`
  WHERE `user_x_user_role`.`user_role_id` = {$clean['student_role']}
  AND `user_x_user_role`.`user_id` = `user`.`user_id`
)
EOL;
        if ($enabledOnly) { // filter out users set to be ignored
            $sql .= ' AND `user`.`enabled` = 1';
        }

        if ($excludeIgnored) { // filter out users set to be ignored
            $sql .= ' AND `user`.`user_sync_ignore` = 0';
        }
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {

                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all non-student users matching a given email address.
     * @param string $email
     * @param boolean $enabledOnly if TRUE then filter out disabled users
     * @param boolean $excludeIgnored if TRUE then filter out users who are flagged to be 'ignored' during the automated user sync process.
     * @return array a nested array of user records
     */
    public function getNonStudentUsersWithEmail ($email, $enabledOnly = false, $excludeIgnored = false)
    {
        $rhett = array();
        $clean = array();
        $clean['email'] = $this->db->escape($email);
        $clean['student_role'] = User_Role::STUDENT_ROLE_ID;
        $sql =<<< EOL
SELECT DISTINCT
`user`.`user_id`,
`user`.`last_name`,
`user`.`first_name`,
`user`.`middle_name`,
`user`.`phone`,
`user`.`email`,
`user`.`primary_school_id`,
`user`.`added_via_ilios`,
`user`.`enabled`,
`user`.`uc_uid`,
`user`.`other_id`,
`user`.`examined`,
`user`.`user_sync_ignore`
FROM `user`
JOIN `user_x_user_role`
ON `user_x_user_role`.`user_id` = `user`.`user_id`
WHERE
`user`.`email` = {$clean['email']}
AND NOT EXISTS (
  SELECT `user_x_user_role`.`user_id`
  FROM `user_x_user_role`
  WHERE `user_x_user_role`.`user_role_id` = {$clean['student_role']}
  AND `user_x_user_role`.`user_id` = `user`.`user_id`
)
EOL;
        if ($enabledOnly) { // filter out users set to be ignored
            $sql .= ' AND `user`.`enabled` = 1';
        }

        if ($excludeIgnored) { // filter out users set to be ignored
            $sql .= ' AND `user`.`user_sync_ignore` = 0';
        }
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {

                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }


    /**
     * Retrieves all non-student users matching the given status criteria.
     * @param boolean $enabledOnly if TRUE then filter out disabled users
     * @param boolean $excludeIgnored if TRUE then filter out users who are flagged to be 'ignored' during the automated user sync process.
     * @return array a nested array of user records
     */
    public function getNonStudentUsers ($enabledOnly = false, $excludeIgnored = false)
    {
        $rhett = array();
        $clean = array();
        $clean['student_role'] = User_Role::STUDENT_ROLE_ID;
        $sql =<<< EOL
SELECT DISTINCT
`user`.`user_id`,
`user`.`last_name`,
`user`.`first_name`,
`user`.`middle_name`,
`user`.`phone`,
`user`.`email`,
`user`.`primary_school_id`,
`user`.`added_via_ilios`,
`user`.`enabled`,
`user`.`uc_uid`,
`user`.`other_id`,
`user`.`examined`,
`user`.`user_sync_ignore`
FROM `user`
JOIN `user_x_user_role`
ON `user_x_user_role`.`user_id` = `user`.`user_id`
WHERE NOT EXISTS (
  SELECT `user_x_user_role`.`user_id`
  FROM `user_x_user_role`
  WHERE `user_x_user_role`.`user_role_id` = {$clean['student_role']}
  AND `user_x_user_role`.`user_id` = `user`.`user_id`
)
EOL;
        if ($enabledOnly) { // filter out users set to be ignored
            $sql .= ' AND `user`.`enabled` = 1';
        }

        if ($excludeIgnored) { // filter out users set to be ignored
            $sql .= ' AND `user`.`user_sync_ignore` = 0';
        }
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {

                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieve the total of user associated with a given school with sync exceptions.
     * This implicitly excludes disabled users and user that are flagged to be ignored from the sync process.
     * @param int $schoolId the id of the school that these users belong to
     * @return int the total number of users with sync exceptions
     */
    public function countUsersWithSyncExceptions ($schoolId = -1)
    {
        $clean = array();

        $sql =<<<EOL
SELECT DISTINCT u.`user_id`
FROM `user` u
JOIN `user_sync_exception` e
ON e.`user_id` = u.`user_id`
WHERE
u.`enabled` = 1
AND u.`user_sync_ignore` = 0
EOL;
        if (0 < $schoolId) {
            $clean['school_id'] = (int) $schoolId;
            $sql .= " AND u.`primary_school_id` = {$clean['school_id']}";
        }

        $query = $this->db->query($sql);
        $rhett =  $query->num_rows();
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves a list of sync exceptions for a given user.
     * @param int $userId the user's id
     * @return array a nested array of sync exception records
     */

    public function getSyncExceptionsForUser ($userId)
    {
        $rhett = array();
        if (empty($userId)) {
            return $rhett;
        }
        $this->db->where('user_id', $userId);
        $query = $this->db->get('user_sync_exception');
        if ($query) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Updates a given user with values stored in associated user sync exceptions.
     * This explicitly "synchronizes" an Ilios internal user with a corresponding user record
     * in an external user store.
     * @param int $userId
     * @param array $syncExceptions
     */
    public function resolveUserDataMismatchFromSyncExceptions ($userId, array $syncExceptions)
    {
        // initial input validation
        if (empty($userId) || empty($syncExceptions)) {
            return;
        }

        // matches sync exception "mismatched property name" values to user property names
        //
        // e.g. sync exceptions may have a mismatching values for the "uid" property on file
        // - this maps to the "uc_uid" property of the corresponding user record
        $propertyNameMap = array(
            'uid' => 'uc_uid',
            'email' => 'email'
        );

        // extract a list of properties that are allowed to be updated from the prop. map
        $allowedSyncExceptionAttributeNames = array_keys($propertyNameMap);

        $updateRow = array(); // holds names/values of user props for the update.

        // iterate of the given sync exceptions and extract the
        foreach ($syncExceptions as $syncException) {
            // PARANOIA MODE ON:
            // check if the given sync exception matches the given user by user-id
            if ($userId != $syncException['user_id']) {
                continue;
            }
            // check if the given sync exception has a property that is allowed to be updated.
            // better safe than sorry...
            if (! in_array($syncException['mismatched_property_name'], $allowedSyncExceptionAttributeNames)) {
                continue;
            }
            // add prop value from sync exception to update-array
            $propertyName = $propertyNameMap[$syncException['mismatched_property_name']];
            $updateRow[$propertyName] = $syncException['mismatched_property_value'];
        }

        // finally, update the user record
        if (count($updateRow)) {
            $this->db->where('user_id', $userId);
            $this->db->update($this->databaseTableName, $updateRow);
        }
    }

    /**
     * Deletes all secondary cohorts associated with a given user.
     * @param int $userId the user id
     */
    public function deleteSecondaryCohorts ($userId)
    {
        $this->db->delete('user_x_cohort', array('user_id' => $userId, 'is_primary' => '0'));
    }

    /**
     * Assigns a given list of secondary cohorts with a given user.
     * @param int $userId the user id
     * @param array $cohortIds a list of cohort ids
     */
    public function setSecondaryCohorts ($userId, array $cohortIds)
    {
        foreach ($cohortIds as $cohortId) {
            $this->_addUserToCohort($userId, $cohortId, false);
        }
    }

    /**
     * Retrieves the enabled ("active") user-account for a given user id.
     * @param int $id the user id
     * @return array|boolean the user record as associative array, or FALSE if none could be found.
     */
    public function getEnabledUserById ($id)
    {
        $rhett = false;

        $this->db->where('user_id', $id);
        $this->db->where('enabled', 1);

        $query = $this->db->get($this->databaseTableName);
        if (0 < $query->num_rows()) {
            $rhett = $query->first_row('array');
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the enabled ("active") user-account by a given API token.
     *
     * @param string $key The API token.
     * @return array|boolean the user record as associative array, or FALSE if none could be found.
     */
    public function getEnabledUserByToken ($key)
    {
        $rhett = false;
        $clean = array();
        $clean['key'] = $this->db->escape($key);

        $sql =<<< EOL
SELECT u.*
FROM user u
JOIN api_key ak ON ak.user_id = u.user_id
WHERE u.enabled = 1
AND ak.api_key = {$clean['key']}
EOL;

        $query = $this->db->get($sql);
        if (0 < $query->num_rows()) {
            $rhett = $query->first_row('array');
        }
        $query->free_result();
        return $rhett;
    }


    /**
     * Retrieves the roles assigned to a given user.
     * @param int $userId the user id.
     * @return array an array of assoc. arrays, each representing a user role record
     */
    protected function _getRoleArray ($userId)
    {
        $rhett = array();

        $this->db->where('user_id', $userId);
        $query = $this->db->get('user_x_user_role');
        foreach ($query->result_array() as $row) {
            $roleRow = $this->roles->getRowForPrimaryKeyId($row['user_role_id']);
            $rhett[] = $roleRow;
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Adds a new user/cohort association.
     * @param int $userId
     * @param int $cohortId
     * @param boolean $isPrimary
     */
    protected function _addUserToCohort ($userId, $cohortId, $isPrimary = false)
    {
        $data = array(
            'user_id' => $userId,
            'cohort_id' => $cohortId
        );
        $this->db->insert('user_x_cohort', $data);
        if ($isPrimary) {
            $this->_setPrimaryCohort($userId, $cohortId);
        }
    }

    /**
     * Checks if a given user is associated with any cohorts.
     * @param int $userId
     * @return boolean
     */
    protected function _hasCohorts ($userId)
    {
        $this->db->from('user_x_cohort');
        $this->db->where('user_id', $userId);
        return ($this->db->count_all_results() ? true : false);
    }

    /**
     * Retrieves a user/cohort association for a given user/cohort combination.
     * @param int $userId
     * @param int $cohortId
     * @return array|boolean an associative array containing the user id, cohort id and is primary flag, or FALSE if not found
     */
    protected function _getUserCohort($userId, $cohortId)
    {
        $rhett = false;
        $query = $this->db->get_where('user_x_cohort', array('user_id' => $userId, 'cohort_id' => $cohortId));
        if ($query->num_rows()) {
            $rhett = $query->result_array(); // if so, then there is only one record. return it.
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Flags a given user/cohort association as "primary".
     * Unflags any pre-existing primary cohort associations for the given user.
     * @param int $userId
     * @param int $cohortId
     */
    protected function _setPrimaryCohort ($userId, $cohortId)
    {
        // unflag all cohort-associations for user
        $data = array('is_primary' => false);
        $this->db->where('user_id', $userId);
        $this->db->update('user_x_cohort', $data);

        // flag user/cohort combo as primary
        $data = array('is_primary' => true);
        $this->db->where('user_id', $userId);
        $this->db->where('cohort_id', $cohortId);
        $this->db->update('user_x_cohort', $data);
    }
 }
