<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Controller for Ilios' user management module.
 */
class Management_Console extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();

        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('Cohort', 'cohort', true);
        $this->load->model('Permission', 'permission', true);
        $this->load->model('Program', 'program', true);
        $this->load->model('School', 'school', true);
        $this->load->model('User', 'user', true);
    }

    /**
     * Default controller action.
     * Prints the user management console view.
     */
    public function index ()
    {
        $data = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        // switch schools if instructed
        $change_school = $this->input->get_post('schoolselect');
        if ($change_school) {
            $this->_setActiveSchool($change_school);
        }

        // get the current school id
        $schoolId = $this->session->userdata('school_id');
        $schoolTitle = null;

        if (! in_array($schoolId, $this->_getAvailableSchools())) {
            // Reset to primary school
            $this->_setActiveSchool($this->session->userdata('primary_school_id'));
            $schoolId = $this->session->userdata('school_id');
        }

        //get the  title of the currently selected school
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);
        if ($schoolRow) {
            $schoolTitle = $schoolRow->title;
        }

        $data['viewbar_title'] = $this->config->item('ilios_institution_name');

        // add school title (and school switcher if applicable) to viewbar
        if ($schoolTitle != null) {
            $key = 'general.phrases.school_of';
            $schoolOfStr = $this->languagemap->getI18NString($key);
            $data['viewbar_title'] .= ' ' . $schoolOfStr . ' ' . $schoolTitle;

            $availSchools = $this->_getAvailableSchools();

            if (count($availSchools) > 1) {
                $school_ids = $availSchools;
                $schools = array();
                foreach ($school_ids as $sid) {
                    $row = $this->school->getRowForPrimaryKeyId($sid);
                    $schools[$sid] = $row->title;
                }
                $data['available_schools'] = $schools;
                $data['selected_school_id'] = $schoolId;

                $key = 'general.phrases.select_school';
                $data['select_school_string'] = $this->languagemap->getI18NString($key);
            }
        }

        $data['manage_login_credentials'] = true; // flag used enable login credentials mngmt. in the UI
        $data['password_required'] = true;
        $authnMethod = $this->config->item('ilios_authentication');

        switch ($authnMethod) {
            case 'shibboleth' :
                $data['manage_login_credentials'] = false;
                $data['password_required'] = false;
                break;
            case 'ldap' :
                $data['password_required'] = false;
                break;
            case 'default' :
            default :
                // do nothing;
        }

        $userRow = $this->user->getRowForPrimaryKeyId($this->session->userdata('uid'));

        $cohorts = $this->cohort->getProgramCohortsGroupedBySchool();

        $data['cohorts_json'] = Ilios_Json::encodeForJavascriptEmbedding($cohorts, Ilios_Json::JSON_ENC_SINGLE_QUOTES);
        $key = 'administration.title';
        $data['page_title'] = $this->languagemap->getI18NString($key);
        $data['title_bar_string'] = $data['page_title'];

        $key = 'administration.ro.title';
        $data['add_ro_title'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.title';
        $data['widget_title'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.dashboard_return';
        $data['dashboard_return_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.data_lists';
        $data['data_lists_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.emails';
        $data['emails_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.passwords';
        $data['manage_passwords_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.permissions';
        $data['permissions_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.system_preferences';
        $data['system_preferences_str'] = $this->languagemap->getI18NString($key);

        $key = 'management.widget.users';
        $data['manage_users_str'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.welcome_back';
        $data['phrase_welcome_back_string'] = $this->languagemap->getI18NString($key);

        $data['cohortless_user_count'] = $this->user->getCountForStudentsWithoutPrimaryCohort($schoolId);
        $data['users_with_sync_exceptions_count'] =  $this->user->countUsersWithSyncExceptions($schoolId);

        $data['school_tree'] = json_encode($this->school->getSchoolTree());

        $this->load->view('management/management_console', $data);
    }

    /**
     * Creates a new Ilios user account.
     * Expected input:
     *     'first_name'
     *     'middle_name'
     *     'last_name'
     *     'email'
     *     'uc_id'
     *     'login'
     *     'password'
     *     'roles'
     * Prints out a JSON-formatted success/error notification on completion/failure.
     */
    public function createUserAccount ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $firstName = $this->input->post('first_name');
        $middleName = $this->input->post('middle_name');
        $lastName = $this->input->post('last_name');
        $email = $this->input->post('email');
        $ucUid = $this->input->post('uc_id');

        $password = $this->input->post('password');
        $username = $this->input->post('login');

        $rolesInput = $this->input->post('roles');
        $roleArray = array();
        if ($rolesInput) {
            $roleArray = explode(",", $rolesInput);
        }
        $schoolId = $this->session->userdata('school_id');

        // identify the primary user role
        $roleIndex = 0;
        $primaryUserRole = count($roleArray) ? $roleArray[$roleIndex] : null; // get the first role
        $roleIndex++;

        $authnMethod = 'default';

        if ($this->config->item('ilios_authentication')) {
            $authnMethod = $this->config->item('ilios_authentication');
        }

        $result = null;
        switch ($authnMethod) {
            case 'shibboleth' : // create a user account without internal login credentials
                $result = $this->_createUserWithoutLoginCredentials($firstName, $lastName, $middleName,
                    $email, $ucUid, $schoolId, $primaryUserRole);
                break;
            case 'ldap' :
                // ignore user input and generate a random password.
                $password = Ilios_PasswordUtils::generateRandomPassword();
                // fall-through intentional!
            case 'default': // create a user account with login credentials
            default:
                $result = $this->_createUserWithLoginCredentials($firstName, $lastName, $middleName, $email,
                    $ucUid, $schoolId, $primaryUserRole, $username, $password);
                break;
        }

        if (is_array($result)) {
            $rhett['error'] = $result;
        } else {
            // go on and add additional user roles
            $userId = $result;
            for (; $roleIndex < count($roleArray); $roleIndex++) {
                $this->user->affectRoleForUser($userId, $roleArray[$roleIndex], true);
            }
            $rhett['success'] = 'User account created.';
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler.
     * Updates login credentials (username/password) for a given user.
     * Expected input:
     *     'user_id' ...the user id
     *     'username' ... the new login handle
     *     'password' ... the new password (optional)
     *
     * Prints out a JSON-formatted success/error messages on completion/failure.
     */
    public function updateLoginCredentials ()
    {
        $rhett = array();
        $updateUsername = false;
        $updatePassword = false;

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }


        $userId = $this->input->get_post('user_id');
        $username = $this->input->get_post('username');
        $password = $this->input->get_post('password');

        // validate input
        if (! $userId) {
            $rhett['error'][] = 'Missing user id.';
        }

        if (! $username) {
            $rhett['error'][] = 'Missing login name.';
        }

        if (array_key_exists('error', $rhett)) {
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        // load the authentication record by user id and by username
        $auth1 = $this->authentication->getByUserId($userId);
        $auth2 = $this->authentication->getByUsername($username);

        if (! $auth1) {
            // check if the user exists
            $user = $this->user->getRowForPrimaryKeyId($userId);
            if (! $user) {
                $rhett['error'][] = 'User account does not exist.';
            }
        }
        // check if the given login name needs to updated
        if (! array_key_exists('error', $rhett)) {
            if ($auth2) {
                // check if the given login name is already taken up by another account
                if ($auth1->person_id !== $auth2->person_id) {
                    $rhett['error'][] = 'The given login name is already in use by another user account.';
                }
            } else {
                $updateUsername = true;
            }
        }

        if ($password) {
            $passwordCheckResult = $this->_validatePassword($password);
            if ($passwordCheckResult !== true) {
                if (array_key_exists('error', $rhett)) {
                    $rhett['error'] = array_merge($rhett['error'], $passwordCheckResult);
                } else {
                    $rhett['error'] = $passwordCheckResult;
                }
            } else {
                // hash the given password
                $salt = $this->config->item('ilios_authentication_internal_auth_salt');
                $hash = Ilios_PasswordUtils::hashPassword($password, $salt);

                // check if the given password needs to be updated
                if (! array_key_exists('error', $rhett)) {
                    if (0 !== strcmp($hash, $auth1->password_sha256)) {
                        $updatePassword = true;
                    }
                }
            }
        }

        if (! array_key_exists('error', $rhett)) {

            if ($updateUsername || $updatePassword) {
                $this->user->startTransaction();
                $success = false;

                if ($updateUsername) {
                    $success = $this->authentication->changeUsername($userId, $username);
                }
                if ($updatePassword) {
                    $success = $this->authentication->changePassword($userId, $hash);
                }
                // change the login name
                if (! $success) {
                    $this->user->rollbackTransaction();
                    $msg = $this->languagemap->getI18NString('general.error.db_update');
                    $rhett['error'][] = $msg;
                } else { // commit the changes
                    $this->user->commitTransaction();
                    $rhett['success'] = 'updated credentials';
                }
            } else {
                $rhett['success'] = 'nothing to update';
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);

    }

    /**
     * Adds login credentials (username/password) for a given user.
     * Expected input:
     *     'user_id' ...the user id
     *     'username' ... the new login handle
     *     'password' ... the new password
     *
     * Prints out a JSON-formatted success/error message on completion/failure.
     */
    public function addLoginCredentials ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }


        $userId = $this->input->post('user_id');
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // validate input
        if (! $userId) {
            $rhett['error'][] = 'Missing user id.';
        }

        if (! $username) {
            $rhett['error'][] = 'Missing login name.';
        }

        $authnMethod = $this->config->item('ilios_authentication');
        switch ($authnMethod) {
            case 'ldap':
                // generate random password.
                $password = Ilios_PasswordUtils::generateRandomPassword();
                break;
            case 'default' :
            default:
                if (! $password) {
                    $rhett['error'][] = 'Missing password.';
                } else {
                    $passwordCheckResult = $this->_validatePassword($password);
                    if ($passwordCheckResult !== true) {
                        if (array_key_exists('error', $rhett)) {
                            $rhett['error'] = array_merge($rhett['error'], $passwordCheckResult);
                        } else {
                            $rhett['error'] = $passwordCheckResult;
                        }
                    }
                }
        }

        if (array_key_exists('error', $rhett)) {
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        // load the authentication record by user id and by username
        $auth1 = $this->authentication->getByUserId($userId);
        $auth2 = $this->authentication->getByUsername($username);

        // check if we already have login credentials for the given user on file
        if (! $auth1) {
            $user = $this->user->getRowForPrimaryKeyId($userId);
            if (! $user) {
                $rhett['error'][] = 'User account does not exist.';
            }
        } else {
            $rhett['error'][] = 'Login credentials for this user account already exist.';
        }

        // check if the given login name is already in use
        if (! array_key_exists('error', $rhett)) {
            if ($auth2) {
                $rhett['error'][] = 'The given login name is already in use by another user account.';
            }
        }

        // hash the given password
        $salt = $this->config->item('ilios_authentication_internal_auth_salt');
        $hash = Ilios_PasswordUtils::hashPassword($password, $salt);

        // add login credentials
        if (! array_key_exists('error', $rhett)) {
            $success = $this->authentication->addNewAuthentication($username, $hash, $userId);
            if (! $success) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');
                $rhett['error'] = $msg;
            } else {
                $rhett['success'] = 'added new login credentials';
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler.
     * Retrieves the program and course permissions for a requested user.
     * Prints out a JSON-formatted array of permissions.
     * @see Permission::getPermissionsForUser()
     */
    public function getUserPermissions ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->input->get_post('user_id');

        $rhett['permissions'] = $this->permission->getPermissionsForUser($userId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler.
     * Retrieves user attributes for a requested user.
     * Prints out a JSON-formatted array of attributes.
     * @see User::getAttributesForUser();
     */
    public function getUserAttributes ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->input->get_post('user_id');

        $attributes = $this->user->getAttributesForUser($userId);

        $attributes['primary_cohort'] = $this->cohort->getPrimaryCohortForUser($userId);
        $attributes['secondary_cohorts'] = $this->cohort->getSecondaryCohortsForUser($userId);
        $attributes['is_student'] = $this->user->userIsStudent($userId);

        $rhett['attributes'] = $attributes;
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler.
     * Prints out a XML-formatted list of programs matching a requested (partial) title.
     */
    public function getProgramList ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $queryResult = $this->program->returnRowsFilteredOnTitleMatch($matchString, true);
        $this->outputQueryResultsAsXML($queryResult);
    }

    /**
     * XHR callback handler.
     * Prints out a XML-formatted list of enabled student-user accounts
     * (associated with the same primary school as the current user)
     * that do not have a primary cohort assignment.
     * @see User::getStudentsWithoutPrimaryCohort()
     */
    public function getCohortlessStudentList ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $queryResult = $this->user->getStudentsWithoutPrimaryCohort($schoolId, true);
        $this->outputQueryResultsAsXML($queryResult);
    }

    /**
     * XHR callback handler.
     * Prints out a JSON-formatted list of users with sync exceptions.
     */
    public function getUsersWithSyncExceptions ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $users = $this->user->getUsersWithSyncExceptions($schoolId);
        $users = $this->_transmogrifySyncExceptionUsers($users);
        $rhett['users'] = $users;
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }


    /**
     * XHR callback handler.
     * Sets given permissions on a given program or course for one or several given users.
     * Prints out a JSON-formatted list of set permissions on success, or an error message on failure.
     */
    public function setUserPermissions ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->input->get_post('user_id');
        $tableName = $this->input->get_post('table_name');
        $replace = $this->input->get_post('replace') == 'true';

        $tableIds = array();
        $idStr = $this->input->get_post('ids');
        if ((! is_null($idStr)) && ($idStr != FALSE) && ($idStr != "")) {
            $tableIds = explode(",", $idStr);
        }

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            $dbError = false;
            $resultingPermIds = array();

            unset($rhett['error']);

            $this->permission->startTransaction();

            if ($replace) {
                $dbError = $this->permission->deletePermissionsForUser($userId, $tableName);
            }

            if (! $dbError) {
                foreach ($tableIds as $tableId) {
                    $permissionId = $this->permission->setPermissionsForUser($userId, $tableName, $tableId, true, true);
                    if (! $permissionId) {
                        $dbError = true;
                        break;
                    } else {
                        array_push($resultingPermIds, $permissionId);
                    }
                }
            }

            if ($dbError) {
                $msg = $this->languagemap->getI18NString('management.error.saving_permissions');

                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->permission);
            } else {
                $permArray = array();
                foreach ($resultingPermIds as $permId) {
                    array_push($permArray, $this->permission->getPermissionObjectForRowId($permId));
                }

                $rhett['permissions'] = $permArray;

                $failedTransaction = false;

                $this->permission->commitTransaction();
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function performCohortAssociations ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $users = json_decode(rawurldecode($this->input->get_post('users')), true);
        $cohortId = $this->input->get_post('cohort_id');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $insertError = false;

            unset($rhett['error']);

            $this->user->startTransaction();

            foreach ($users as $userObj) {
                $this->user->enableUser($userObj['user_id'], true);
                $this->user->assignPrimaryCohort($userObj['user_id'], $cohortId);
                $insertError = $this->user->transactionAtomFailed();

                if ($insertError) {
                    break;
                }
            }

            if ($insertError) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');

                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->user);
            } else {
                $rhett['result'] = "apparent success";

                $failedTransaction = false;

                $this->user->commitTransaction();
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler.
     * Prints out JSON-formatted array containing totals for various groups of actionable user accounts,
     * including cohortless students and accounts with raised user synchronization issues.
     */
    public function getUserAccountAlertCounts ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $rhett['cohortless_user_count'] = $this->user->getCountForStudentsWithoutPrimaryCohort($schoolId);
        $rhett['users_with_sync_exceptions_count'] = $this->user->countUsersWithSyncExceptions($schoolId);
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Updates a user account.
     * Expected input:
     *     'user_id'
     *     'roles'
     *     'sync_ignored'
     *     'secondary_cohorts'
     *     'set_able'
     * Prints out a JSON formatted array containing either a "success" message or "error" messages.
     */
    public function updateUserAccount ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->input->get_post('user_id');
        $shouldAffectSyncIgnore = false;
        $syncIgnore = false;
        if ($this->input->get_post('sync_ignored')) {
            $shouldAffectSyncIgnore = true;
            $syncIgnore = ($this->input->get_post('sync_ignored') == 'y');
        }
        $rolesInput = $this->input->get_post('roles');
        $roleArray = array();
        if ($rolesInput) {
            $roleArray = explode(",", $rolesInput);
        }

        $secondaryCohortIds = array();
        if ($this->input->get_post('secondary_cohorts')) {
            $secondaryCohortIds = explode(",", $this->input->get_post('secondary_cohorts'));
        }

        $shouldAffectEnable = false;
        $setToEnable = false;

        if ($this->input->get_post('set_able')) {
            $shouldAffectEnable = true;
            $setToEnable = ($this->input->get_post('set_able') == 'enable');
        }

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {

            $this->user->startTransaction();

            if ($shouldAffectSyncIgnore) { // set user to be excluded from sync process
                 $this->user->setSyncIgnoreBit($userId, $syncIgnore);
            }

            // update user attributes
            if ($shouldAffectEnable) { // enable/disable user
                $this->user->enableUser($userId, $setToEnable);
            } else {
                // assign/unassign user role
                $availableRoleSet = array(
                    User_Role::COURSE_DIRECTOR_ROLE_ID,
                    User_Role::DEVELOPER_ROLE_ID,
                    User_Role::FACULTY_ROLE_ID
                );

                $unselectedRoles = array_diff($availableRoleSet, $roleArray);

                // associate user roles
                for ($roleIndex = 0; $roleIndex < count($roleArray); $roleIndex++) {
                    $this->user->affectRoleForUser($userId, $roleArray[$roleIndex], true);
                }

                // unassociate user roles
                foreach ($unselectedRoles as $roleId) {
                    $this->user->affectRoleForUser($userId, $roleId, false);
                }

                // assign/unassign secondary cohort
                $this->user->deleteSecondaryCohorts($userId);
                $this->user->setSecondaryCohorts($userId, $secondaryCohortIds);
            }

            if ($this->user->transactionAtomFailed()) {
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->user);
            } else {
                $this->user->commitTransaction();
                $failedTransaction = false;
            }

        } while ($failedTransaction && ($transactionRetryCount > 0));

        if ($failedTransaction) {
            $msg = $this->languagemap->getI18NString('general.error.db_update');
            $rhett['error'] = $msg;
        } else {
            $rhett['result'] = "i invented the piano key necktie";
        }
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR callback handler function.
     * Takes actions on users with sync exceptions.
     * Expected input is a HTTP Post with JSON encoded <code>users</code> object,
     * which provides key/value pairs of user ids/action-names for each Ilios user
     * and the corresponding action to be taken.
     * E.g.
     * <pre>
     * users = {
     *    4  : "update",
     *    5  : "ignore",
     *    10 : "disable",
     *    ...
     * }
     * </pre>
     *
     * On success, a JSON encoded array containing a success message and the total number
     * of remaining sync exceptions is printed out.
     * <code>
     * {
     *     'result' : 'success',
     *     'users_with_sync_exceptions_count' : # OF REMAINING SYNC EXCEPTIONS
     * }
     * </code>
     * On failure, a JSON encoded array containing an error message is printed out.
     * <code>
     *     'error' : WHATEVER ERROR MESSAGE
     * </code>
     */
    public function processActionItemsForUserSyncExceptions ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $allowedActions = array('ignore', 'disable', 'update');

        $actionItems = json_decode(rawurldecode($this->input->get_post('users')), true);

        if (is_array($actionItems) && count($actionItems)) {
            $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
            do {
                $failedTransaction = true;
                $updateError = false;
                unset($rhett['error']);
                $this->user->startTransaction();

                foreach ($actionItems as $userId => $action) {
                    if (in_array($action, $allowedActions)) { // sanity check
                        // 1. take action on the user account

                        switch ($action) {
                            case 'disable' : // disable user
                                $this->user->enableUser($userId, false);
                                break;
                            case 'update' : // sync up user with external user store
                                $syncExceptions = $this->user->getSyncExceptionsForUser($userId);
                                $this->user->resolveUserDataMismatchFromSyncExceptions($userId, $syncExceptions);
                                break;
                            case 'ignore' :
                            default :
                                // do nothing, this is the default.

                        }
                        // 2. delete associated sync exceptions
                        $this->user->deleteSyncExceptionsForUser($userId);

                        // check transaction status
                        if (! $updateError) {
                            $updateError = $this->user->transactionAtomFailed();
                        }

                    } else {
                        // TODO: figure out how to proceed on given actions
                        // that are not implemented
                        // for now, we just ignore them.
                    }
                }

                if ($updateError) {
                    $msg = $this->languagemap->getI18NString('general.error.db_insert');
                    $rhett['error'] = $msg;
                    Ilios_Database_TransactionHelper::failTransaction(
                        $transactionRetryCount, $failedTransaction, $this->user);
                } else {
                    $rhett['result'] = "success";
                    $failedTransaction = false;
                    $this->user->commitTransaction();
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));
        }


        $rhett['result'] = "success";

        // tally up the remaining sync exceptions and add them to the return values.
        $schoolId = $this->session->userdata('school_id');
        $rhett['users_with_sync_exceptions_count'] = $this->user->countUsersWithSyncExceptions($schoolId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR Handler.
     * Prints out a JSON-formatted array of users.
     * Expects the following values to be POSTed:
     * - 'query' ... a name/name-fragment to search users by
     */
    public function searchAllUsers ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $queryResults = $this->user->getUsersFilteredOnName($matchString, true);
        $this->outputQueryResultsAsXML($queryResults);
    }

    /**
     * XHR Handler.
     * Prints out a JSON-formatted array of enabled users.
     * Expects the following values to be POSTed:
     * - 'query' ... a name/name-fragment to search users by
     */
    public function searchEnabledUsers ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $queryResults = $this->user->getUsersFilteredOnName($matchString, false);

        $this->outputQueryResultsAsXML($queryResults);
    }

    /**
     * XHR handler.
     * Prints out an XML-formatted list of courses.
     * Expects the following values to be POSTed:
     * - 'query' ... a title/title-fragment to search courses by
     *
     */
    public function getCourseList ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $title = $this->input->get_post('query');
        $schoolId = $this->session->userdata('school_id');
        $uid = $this->session->userdata('uid');
        $queryResults = $this->course->getCoursesFilteredOnTitleMatch($title, $schoolId, $uid);

        $this->outputQueryResultsAsXML($queryResults);
    }

    /**
     * XHR callback handler.
     * Prints out a XML-formatted list of programs matching a requested (partial) title.
     */
    public function getSchoolList ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $queryResult = $this->school->returnRowsFilteredOnTitleMatch($matchString, true);
        $this->outputQueryResultsAsXML($queryResult);
    }

    /**
     * Transforms and de-dupes a given list of user data for output.
     * @param array $rows
     * @return array
     */
    protected function _transmogrifySyncExceptionUsers (array $rows = array())
    {
        $rhett = array();

        if (! count($rows)) {
            return $rhett;
        }

        // these are the codes for sync exceptions that
        // can be resolved by updating the Ilios internal user
        // record with the user data provided from the external user store
        $exceptionsResolvableBySyncingAttributes = array(
                Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_EMAIL_MISMATCH,
                Ilios_UserSync_Process_UserException::NON_STUDENT_SYNC_UID_MISMATCH,
                Ilios_UserSync_Process_UserException::STUDENT_SYNC_EMAIL_MISMATCH);

        $schoolsMap = $this->school->getSchoolsMap(false);
        $rolesMap = $this->roles->getUserRolesMap();

        // cache retrieved cohort titles
        $cohortNameCache = array();

        // process/transform retrieved records by constructing
        // a data structure based on nested (associative) arrays.
        //
        // Each user is represented by an associative array.
        //
        // - User info, such as first/last name, email etc stored as key/value pairs
        //
        // - Exceptions are contained in a nested array of associative arrays under the 'exceptions' key
        // with a corresponding array 'exception_codes' which contains *only* the exceptions codes.
        //
        // - User roles assigned to each user are contained in
        // a list of role titles under under the 'roles' key.

        foreach ($rows as $row) {
            $userId = $row['user_id'];
            $userData = array();

        // instantiate user data subarray on demand
            if (! array_key_exists($userId, $rhett)) {
                $userData['user_id'] = $row['user_id'];
                $userData['first_name'] = $row['first_name'];
                $userData['last_name'] = $row['last_name'];
                $userData['middle_name'] = $row['middle_name'];
                $userData['email'] = $row['email'];
                $userData['phone'] = $row['phone'];
                $userData['school_id'] = $row['primary_school_id'];
                $userData['school_name'] = '';

                if (array_key_exists($userData['school_id'], $schoolsMap)) {
                    $userData['school_name'] = $schoolsMap[$userData['school_id']]['title'];
                }

                $userData['uc_uid'] = $row['uc_uid'];
                $userData['enabled'] = (boolean) $row['enabled'];
                $userData['user_sync_ignore'] = (boolean) $row['user_sync_ignore'];
                $userData['roles'] = array();
                $userData['exceptions'] = array();
                $userData['exception_codes'] = array();
                // flag to indicate whether "update" (syncing attributes) is a valid option
                // be default, this option is FALSE
                // the flag will be set to TRUE based on the sync exceptions on file
                // for this user, if applicable, further downstream
                $userData['update_option'] = false;
                $userData['cohort_name'] = '';

                // get full name of the cohort that this user is associated with.
                $cohortId = $row['cohort_id'];
                if ($cohortId) {
                    if (! array_key_exists($cohortId, $cohortNameCache)) { // check cache
                        //  get cohort title and cache it
                        $cohortName = $this->cohort->getFullCohortTitle($cohortId);
                        $cohortNameCache[$cohortId] = $cohortName;
                    }
                    $userData['cohort_name'] = $cohortNameCache[$cohortId];
                }

                $rhett[$userId] = $userData;
            }

            // get the user data sub-array corresponding to the current resultset row
            $userData = $rhett[$userId];

            // process and roll up exceptions per user
            if (! in_array($row['exception_code'], $userData['exception_codes'])) {
                $exception = array();
                $exception['exception_id'] = $row['exception_id'];
                $exception['exception_code'] = $row['exception_code'];
                $exception['mismatched_property_name'] = $row['mismatched_property_name'];
                $exception['mismatched_property_value'] = $row['mismatched_property_value'];
                $userData['exception_codes'][] = $exception['exception_code'];
                $userData['exceptions'][] = $exception;
                if (in_array($row['exception_code'], $exceptionsResolvableBySyncingAttributes)) {
                    $userData['update_option'] = true; // enable "update"-action as viable option
                }
            }

            // roll up roles per user
            if (array_key_exists($row['user_role_id'], $rolesMap)) {
                $role = $rolesMap[$row['user_role_id']];
                if (! in_array($role, $userData['roles'])) {
                    $userData['roles'][] = $role;
               }
            }

        // store the updated user data back in the list of return values.
            $rhett[$userId] = $userData;
        }
        return array_values($rhett);
    }

    /**
     * Validates a given password and returns error messages if the password
     * fails one or more validation criteria.
     * @param string $password
     * @return array | boolean an array of error messages, or TRUE on success.
     */
    protected function _validatePassword ($password)
    {
        $rhett = array();
        $result = Ilios_PasswordUtils::checkPasswordStrength($password);

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_OK === $result) {
            return true;
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_SHORT & $result) {
            $rhett[] = 'The given password is too short.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_TOO_LONG & $result) {
            $rhett[] = 'The given password is too long.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_INVALID_CHARS & $result) {
            $rhett[] = 'The given password contains invalid characters.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_DIGIT_MISSING & $result) {
            $rhett[] = 'The given password does not contain any digits.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING & $result) {
            $rhett[] = 'The given password does not contain any lower-case characters.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING & $result) {
            $rhett[] = 'The given password does not contain any upper-case characters.';
        }

        if (Ilios_PasswordUtils::PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING & $result) {
            $rhett[] = 'The given password does not contain any special characters.';
        }
        return $rhett;
    }
    /**
     * Creates a new user account and corresponding login credentials based on the given input.
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $email
     * @param string $ucUid
     * @param int $schoolId
     * @param int $primaryUserRole
     * @param string $username
     * @param string $password
     * @return array|int an array of error messages, or the id of the newly created user
     */
    protected function _createUserWithLoginCredentials ($firstName, $lastName, $middleName,
        $email, $ucUid, $schoolId, $primaryUserRole, $username, $password)
    {
        $errors = array();

        $userId = $this->session->userdata('uid');

        // check if username was provided
        if (! $username) {
            $errors[] = 'Missing login name.';
        } else {
            // check if username is already taken
            $auth = $this->authentication->getByUsername($username);
            if ($auth) {
                $errors[] = $this->languagemap->getI18NString('administration.error.already_exists_status');
            }
        }

        // check if password was provided
        if (! $password) {
            $errors[] = 'Missing password.';
        } else { // check password strength
            $passwordCheckResult = $this->_validatePassword($password);
            if ($passwordCheckResult !== true) {
                $errors = array_merge($errors, $passwordCheckResult);
            }
        }

        if (! count($errors)) {
            // hash the given password
            $salt = $this->config->item('ilios_authentication_internal_auth_salt');
            $hash = Ilios_PasswordUtils::hashPassword($password, $salt);

            $this->user->startTransaction();

            $atoms = array();
            $newUserId = $this->user->addUser($lastName, $firstName, $middleName, null,
                $email, $ucUid, null, $schoolId, null, $primaryUserRole, $atoms);

            if (($newUserId == -1) || (! $this->authentication->addNewAuthentication($username, $hash, $newUserId))) {
                $this->user->rollbackTransaction();

                $msg = $this->languagemap->getI18NString('general.error.db_insert');

                $errors[] = $msg;
            } else {
                $this->user->commitTransaction();

                $atoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newUserId, 'user_id', 'user',
                    Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($atoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
        }

        if (count($errors)) {
            return $errors;
        }
        return $newUserId;
    }

    /**
     * Creates a new user account without corresponding login credentials based on the given input.
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $email
     * @param string $ucUid
     * @param int $schoolId
     * @param int $primaryUserRole
     * @return array|int an array of error messages, or the id of the newly created user
     */
    protected function _createUserWithoutLoginCredentials ($firstName, $lastName, $middleName,
        $email, $ucUid, $schoolId, $primaryUserRole)
    {
        $errors = array();

        $userId = $this->session->userdata('uid');

        $this->user->startTransaction();
        $atoms = array();
        $newUserId = $this->user->addUser($lastName, $firstName, $middleName, null,
            $email, $ucUid, null, $schoolId, null, $primaryUserRole, $atoms);

        if ($newUserId == -1) {
            $this->user->rollbackTransaction();
            $msg = $this->languagemap->getI18NString('general.error.db_insert');
            $errors[] = $msg;
        } else {
            $this->user->commitTransaction();

             $atoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newUserId, 'user_id', 'user',
                 Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);


            // save audit trail
            $this->auditAtom->startTransaction();
            $success = $this->auditAtom->saveAuditEvent($atoms, $userId);
            if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                $this->auditAtom->rollbackTransaction();
            } else {
                $this->auditAtom->commitTransaction();
            }
        }

        if (count($errors)) {
            return $errors;
        }
        return $newUserId;
    }
}
