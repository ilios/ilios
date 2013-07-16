<?php
include_once "abstract_ilios_controller.php";

/**
 * @package Ilios
 *
 * Learner-group management controller.
 */
class Group_Management extends Abstract_Ilios_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Group', 'group', true);
        $this->load->model('Instructor_Group', 'instructorGroup', true);
        $this->load->model('School', 'school', true);
        $this->load->model('User', 'user', true);
    }

    /**
     * Default action.
     * Prints out the learner groups management page.
     * Can process the following request paramters
     *     "group_id" ... learner group id [optional]
     *     "cohort_id" ... cohort id [optional]
     */
    public function index ()
    {
        // authentication check
        if ($this->divertedForAuthentication) {
            return;
        }

        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_viewAccessForbiddenPage($lang, $data);
            return;
        }

        $this->output->set_header('Expires: 0');

        $user = $this->user->getRowForPrimaryKeyId($data['user_id']);

        $schoolId = $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if ($schoolRow != null) {
            $data['school_id'] = $schoolId;
            $data['school_name'] = $schoolRow->title;

            $data['viewbar_title'] = $data['institution_name'];
            if ($schoolRow->title != null) {
                $key = 'general.phrases.school_of';
                $schoolOfStr = $this->i18nVendor->getI18NString($key, $lang);
                $data['viewbar_title'] .= ' ' . $schoolOfStr . ' ' . $schoolRow->title;
            }


            $cohortStub = false;

            $groupId =  (int) $this->input->get('group_id');
            $cohortId = (int) $this->input->get('cohort_id');

            if ($cohortId) {
                $cohortStub = $this->_getProgramCohortStubByCohort($cohortId);
            } elseif ($groupId) {
                $cohortStub = $this->_getProgramCohortStubByGroup($groupId);
            }

            $data['cohort_load_stub'] = $cohortStub;

            $key = 'groups.title_bar';
            $data['title_bar_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.select_program';
            $data['select_program_link_string'] = $this->i18nVendor->getI18NString($key,
                                                                                   $lang);

            $key = 'groups.page_header';
            $data['page_header_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.instructor_picker_title';
            $data['instructor_picker_title'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'general.phrases.expand_all';
            $data['expand_groups_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.open_cohort';
            $data['open_cohort_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.default_instructor';
            $data['default_instructor_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.default_location';
            $data['default_location_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.add_new_to_all_group';
            $data['add_to_all_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'groups.add_new_group';
            $data['add_group_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'general.phrases.current_enrollment';
            $data['current_enrollment_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'general.phrases.orphan_members';
            $data['orphans_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'general.phrases.program_title_short';
            $data['program_title_short_string'] = $this->i18nVendor->getI18NString($key,
                                                                                   $lang);
            $key = 'groups.program_title';
            $data['program_title_full_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $key = 'general.terms.filter';
            $data['word_filter_string'] = $this->i18nVendor->getI18NString($key, $lang);

            $this->populateForAddNewMembersDialog($data, $lang);
            $this->populateI18NStringsForContentContainerGenerator($data, $lang);

            $this->load->view('group/group_manager', $data);
        } else {
            // error condition
        }
    }


    /**
     * @todo add code docs
     */
    public function getUsersForCohort ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $cohortId = $this->input->get_post('cohort_id');

        $userArray = $this->user->getUsersForCohortAsArray($cohortId);

        $rhett['XHRDS'] = $userArray;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    // called as an XHRDataSource
    public function getUserGroupTree ()
    {
        $rhett = array();
        $lang = $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $matchString = $this->input->get('query');
        $cohortId = $this->input->get('cohort_id');

        $groupIds = $this->cohort->getGroupIdsForCohortWithId($cohortId);
        $groupsArray = array();
        foreach ($groupIds as $groupId) {
            $treeArray = $this->group->getUserGroupTreeFilteredOnUserNameAndCohort($matchString,
                                                                                   $cohortId,
                                                                                   $groupId);

            $groupsArray[$groupId] = $treeArray;
        }

        $rhett = array();
        $rhett['XHRDS'] = $groupsArray;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      'num_groups'
     *      'cohort_id'
     *      'group_id'
     *
     * @return an array with a single key 'error' or a JSON'd, XHRDataSource-happy-keeping, array
     *              of N group model trees where N is the num_groups argument
     */
    public function autogenerateSubGroups ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $cohortId = (int) $this->input->get_post('cohort_id');
        $groupToDivide = (int) $this->input->get_post('group_id');
        $numberOfSubgroupsToCreate = (int) $this->input->get_post('num_groups');

        if (-1 == $groupToDivide) {

            $msg = $this->i18nVendor->getI18NString('groups.error.cohort_association', $lang);
            $rhett['error'] = $msg . ' ' . $groupToDivide;
        } else {

            $enrollment = $this->group->getUserCountForGroupWithId($groupToDivide);

            if ($enrollment == 0) {
                $lang = $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('groups.error.cohort_zero_population',
                                                        $lang);

                $rhett['error'] = $msg . ' ' . $groupToDivide;
            } else {
                $firstSizeA = floor($enrollment / $numberOfSubgroupsToCreate);
                $lastSizeA = $enrollment - (($numberOfSubgroupsToCreate - 1) * $firstSizeA);
                if ($lastSizeA == 0) {
                    $lastSizeA = $firstSizeA;
                }

                $firstSizeB = ceil($enrollment / $numberOfSubgroupsToCreate);
                $lastSizeB = $enrollment - (($numberOfSubgroupsToCreate - 1) * $firstSizeB);
                if ($lastSizeB == 0) {
                    $lastSizeB = $firstSizeB;
                }

                if (abs($lastSizeB - $firstSizeB) < abs($lastSizeA - $firstSizeA)) {
                    $firstNMinusOneSize = $firstSizeB;
                    $lastSize = $lastSizeB;
                } else {
                    $firstNMinusOneSize = $firstSizeA;
                    $lastSize = $lastSizeA;
                }

                $failedTransaction = true;
                $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
                do {
                    $auditAtoms = array();

                    unset($rhett['error']);

                    $this->group->startTransaction();

                    for ($i = 0, $n = ($numberOfSubgroupsToCreate - 1); $i < $n; $i++) {
                        $this->group->makeSubgroupOfUnassignedUsersFromCohortId($firstNMinusOneSize,
                            $cohortId, $groupToDivide, ($i + 1), $auditAtoms);

                        if ($this->group->transactionAtomFailed()) {
                            $rhett['error'] = "There was a Database Deadlock error.";

                            break;
                        }
                    }

                    if (! isset($rhett['error'])) {
                        $this->group->makeSubgroupOfUnassignedUsersFromCohortId($lastSize, $cohortId,
                            $groupToDivide, $numberOfSubgroupsToCreate, $auditAtoms);
                    }

                    if (isset($results['error']) || $this->group->transactionAtomFailed()) {
                        $rhett['error'] = "There was a Database Deadlock error.";
                        $this->failTransaction($transactionRetryCount, $failedTransaction, $this->group);
                    } else {
                        $failedTransaction = false;
                        $this->group->commitTransaction();
                        $this->auditEvent->saveAuditEvent($auditAtoms);
                    }
                } while ($failedTransaction && ($transactionRetryCount > 0));
            }
        }

        if (isset($rhett['error'])) {
            header("Content-Type: text/plain");
            echo json_encode($rhett);
        } else {
            $rhett = array();
            $rhett['XHRDS'] = $this->group->getSubgroupsForGroupId($groupToDivide);
            header("Content-Type: text/plain");
            echo json_encode($rhett);
        }
    }

    /**
     * XHR handler.
     *
     * Retrieves and prints out the groups trees for a given cohort.
     *
     * Expected request parameters:
     *     "cohort_id" ... the corort identifier
     *
     * Prints a JSON formatted array of group model arrays keyed off by "XHRDS".
     * Each group model is an associative array keyed off by:
     *     "group_id" ... the group identifier
     *     "title" ... the group title
     *     "parent_group_id" ... the parent group id (NULL for top level groups)
     *     "location" ... the group room/learning location
     *     "instructors" ... array of instructors (user records)
     *     "users" ... array of learners (user records)
     *     "subgroups" ... array of nested sub-groups
     *     "courses" .. array of associated courses
     */
    public function getMasterGroupsForCohort ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $cohortId = $this->input->get_post('cohort_id');

        $groupIds = $this->cohort->getGroupIdsForCohortWithId($cohortId);
        $groups = $this->_getGroupsForGroupIds($groupIds);

        if (false === $groups) {
            $msg = $this->i18nVendor->getI18NString('groups.error.failed_subgroup_fetch', $lang);
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
        }

        for ($i = 0, $n = count($groups); $i < $n; $i++) {
            $this->_stripdownGroupMembership($groups[$i]);
        }

        $rhett['XHRDS'] = $groups;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR Handler.
     * Deletes a given group and its sub-groups.
     * Expected params:
     *      'group_id'
     *      'container_number'
     *
     * @return a json'd array with keys 'group_id' and 'container_number', or 'error'
     */
    public function deleteGroup ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $groupId = $this->input->get_post('group_id');
        $containerNumber = $this->input->get_post('container_number');

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->group->startTransaction();

            if ($this->group->deleteGroupWithGroupId($groupId, $auditAtoms)
                                            && (! $this->group->transactionAtomFailed())) {
                $rhett['group_id'] = $groupId;
                $rhett['container_number'] = $containerNumber;

                $failedTransaction = false;

                $this->group->commitTransaction();

                $this->auditEvent->saveAuditEvent($auditAtoms);
            } else {
                $lang = $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('groups.error.failed_group_delete', $lang);

                $rhett['error'] = $msg . ': ' . $groupId;

                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->group);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @return a json'd array with key 'success' on success, or 'error' on failure
     */
    public function saveGroupModelTree ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

            // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        /**
         * game plan:
         *      x get all group_id of subgroups
         *      x delete all entries in group_x_user for those group ids and this root group id
         *      x update title, instructors, location for each subgroup
         *      x make new group_x_user entries for all users in subgroups
         *      x update title, instructors, location for this root group
         */
        $wholeTree = json_decode(rawurldecode($this->input->get_post('whole_model_glom')), true);

        // backfill membership associations in the group tree
        $subgroups = $wholeTree['subgroups'];
        for ($i = 0, $n = count($subgroups); $i < $n; $i++) {
            $this->_backfillGroupMemberships($subgroups[$i]);
        }
        $wholeTree['subgroups'] = $subgroups;

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            $this->group->startTransaction();

            $rhett = array();

            $subgroups = $wholeTree['subgroups'];
            $len = count($subgroups);
            for ($i = 0; (($i < $len) && (! isset($rhett['error']))); $i++) {
                $subgroup = $subgroups[$i];

                $result = $this->_recursivelySaveGroupTree($subgroup['group_id'], $subgroup['title'],
                                                          $subgroup['instructors'],
                                                          $subgroup['location'],
                                                          $subgroup['parent_group_id'],
                                                          $subgroup['users'],
                                                          $subgroup['subgroups'],
                                                          $auditAtoms);

                if ($result != null) {
                    $rhett['error'] = $result['error'];
                }
            }

            if (! isset($rhett['error'])) {
                $rhett['success'] = 'indeedy';

                $failedTransaction = false;

                $this->group->commitTransaction();

                $this->auditEvent->saveAuditEvent($auditAtoms);
            } else {
                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->group);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This will insert all the users in the CSV file into the db in the student role associated to
     *      the specified cohort. The columns, in order, expected in the CSV file are:
     *              Last name
     *              First name
     *              Middle name
     *              Phone
     *              EMail address
     *              UC id
     *              GALEN id
     *              Other id
     *
     * Expected parameters:
     *          . 'cohort_id'
     *
     * @return a json'd array with either the keys 'error' & potentially 'duplicates',
     *              or the key 'users'
     */
    public function uploadStudentListCSVFile ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $uploadPath = './tmp_uploads/'; // @todo make this configurable.

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '5000'; // 5000 KB

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload()) {
            $lang = $this->getLangToUse();
            $msg = $this->i18nVendor->getI18NString('general.error.upload_fail', $lang);
            $msg2 = $this->i18nVendor->getI18NString('general.phrases.found_mime_type', $lang);
            $uploadData = $this->upload->data();

            $rhett['error'] = $msg . ': ' . $this->upload->display_errors() . '. ' . $msg2 . ': '
                                . $uploadData['file_type'];
        } else {
            $uploadData = $this->upload->data();
            $cohortId = $this->input->get_post('cohort_id');
            $newUsers = array();

            $this->load->library('csvreader');

            // false parameter => no named fields on line 0 of the csv
            $csvData = $this->csvreader->parse_file($uploadData['full_path'], false);

            $foundDuplicates = array();

            foreach ($csvData as $row) {
                $email = $row[4];

                if ($this->user->userExistsWithEmail($email)) {
                    array_push($foundDuplicates, ($email . ' ' . $row[0] . ', ' . $row[1] . ' ' . $row[2]));
                }
            }

            // MAY RETURN THIS BLOCK
            if (count($foundDuplicates) > 0) {
                $lang = $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('general.error.duplicate_users_found',
                                                        $lang);

                $rhett['duplicates'] = $foundDuplicates;
                $rhett['error'] = $msg;

                if (! unlink($uploadData['full_path'])) {
                    log_message('warning', 'Was unable to delete uploaded CSV file: ' . $uploadData['orig_name']);
                }

                header("Content-Type: text/plain");
                echo json_encode($rhett);

                return;
            }

            $failedTransaction = true;
            $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
            do {
                $auditAtoms = array();

                unset($rhett['error']);

                $this->user->startTransaction();

                foreach ($csvData as $row) {
                    $lastName = trim($row[0]);
                    $firstName = trim($row[1]);
                    $middleName = trim($row[2]);
                    $phone = trim($row[3]);
                    $email = trim($row[4]);
                    $ucUID = trim($row[5]);
                    $otherId = trim($row[7]);

                    $primarySchoolId = $this->session->userdata('school_id');

                    $newId = $this->user->addUserAsStudent($lastName, $firstName, $middleName, $phone,
                        $email, $ucUID, $otherId, $cohortId, $primarySchoolId, $auditAtoms);

                    if (($newId <= 0) || $this->user->transactionAtomFailed()) {
                        $lang = $this->getLangToUse();
                        $msg = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);

                        $rhett['error'] = $msg;

                        break;
                    }

                    array_push($newUsers, $this->convertStdObjToArray($this->user->getRowForPrimaryKeyId($newId)));
                }

                if (isset($rhett['error'])) {
                    $this->failTransaction($transactionRetryCount, $failedTransaction, $this->user);
                } else {
                    $this->user->commitTransaction();

                    $failedTransaction = false;

                    $this->auditEvent->saveAuditEvent($auditAtoms);

                    $rhett['users'] = $newUsers;
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));
            if (! unlink($uploadData['full_path'])) {
            	log_message('warning', 'Was unable to delete uploaded CSV file: ' . $uploadData['orig_name']);
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Called via the Edit Members (or whatever) dialog for the db addition of a new user (given
     *  a cohort_id) -- an entry in the user table is made.
     *
     * @return a json'd array with either the key 'error', or the key pair 'user' and
     *              'container_number' (the latter being a passback from the incoming param)
     */
    public function addNewUserToGroup ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $cohortId = $this->input->get_post('cohort_id');
        $containerNumber = $this->input->get_post('container_number');
        $lastName = trim($this->input->get_post('last_name'));
        $firstName = trim($this->input->get_post('first_name'));
        $middleName = trim($this->input->get_post('middle_name'));
        $phone = trim($this->input->get_post('phone'));
        $email = trim($this->input->get_post('email'));
        $ucUID = trim($this->input->get_post('uc_uid'));

        // MAY RETURN THIS BLOCK
        if ($this->user->userExistsWithEmail($email)) {
            $lang = $this->getLangToUse();
            $msg = $this->i18nVendor->getI18NString('general.error.duplicate_user_found', $lang);

            $rhett['error'] = $msg;

            header("Content-Type: text/plain");
            echo json_encode($rhett);

            return;
        }

        $primarySchoolId = $this->session->userdata('school_id');

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->user->startTransaction();

            $newId = $this->user->addUserAsStudent($lastName, $firstName, $middleName, $phone, $email,
                                                   $ucUID, '', $cohortId, $primarySchoolId,
                                                   $auditAtoms);

            if (($newId <= 0) || $this->user->transactionAtomFailed()) {
                $lang = $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);

                $rhett['error'] = $msg;

                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->user);
            }
            else {
                $rhett['container_number'] = $containerNumber;
                $rhett['user'] = $this->user->getRowForPrimaryKeyId($newId);

                $failedTransaction = false;

                $this->user->commitTransaction();

                $this->auditEvent->saveAuditEvent($auditAtoms);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /*
     * Expected parameters:
     *  . 'group_id'
     *  . 'cohort_id'
     *  . 'next_container'
     *
     * If group_id == -1 then a new cohort-master-group will be made, and the group will be
     *  populated with all of the users in the cohort
     *
     * @return a json'd array with either the key 'error', or the keys group_id, title, and
     *              container_number (which is a passthrough of next_container)
     */
    public function addNewGroup ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $cohortId = $this->input->get_post('cohort_id');
        $groupId = $this->input->get_post('group_id');
        $containerNumber = $this->input->get_post('next_container');

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            $this->group->startTransaction();

            $rhett = $this->group->addNewGroup($cohortId, $groupId, $containerNumber, $auditAtoms);

            if (isset($rhett['error']) || $this->group->transactionAtomFailed()) {
                if (! isset($rhett['error'])) {
                    $rhett['error'] = "There was a database deadlock exception.";
                }

                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->group);
            } else {
                $rhett['container_number'] = $containerNumber;

                $failedTransaction = false;


                $this->group->commitTransaction();

                $this->auditEvent->saveAuditEvent($auditAtoms);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Retrieves program and cohort information for a given cohort.
     * @param int $cohortId the cohort id
     * @return boolean|array an assoc. array with cohort/program info, or FALSE if none could be found
     * @todo move this method into a model
     */
    protected function _getProgramCohortStubByCohort ($cohortId)
    {
        $rhett = false;
        $clean = array();
        $clean['cohort_id'] = (int) $cohortId;
        $query =<<< EOL
SELECT
  `program`.`title` AS `program_title`,
  `program`.`short_title` AS `program_short_title`,
  `program`.`duration` AS `program_duration`,
  `cohort`.`cohort_id` AS `cohort_id`,
  `cohort`.`title` AS `cohort_title`,
  `cohort`.`program_year_id` AS `program_year_id`
FROM
  `program`
  JOIN `program_year` ON `program`.`program_id` = `program_year`.`program_id`
  JOIN `cohort` ON `program_year`.`program_year_id` = `cohort`.`program_year_id`
WHERE
  `cohort`.`cohort_id` = {$clean['cohort_id']}
ORDER BY `program_title`, `cohort_title`
EOL;

        $DB = $this->dbHandle;
        $queryResults = $DB->query($query);

        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();
            $rhett = array();
            $rhett['program_title'] = $row->program_title;
            $rhett['cohort_id'] = $row->cohort_id;
            $rhett['cohort_title'] = $row->cohort_title;
            $rhett['program_year_id'] = $row->program_year_id;
            $rhett['program_short_title'] = $row->program_short_title;
            $rhett['program_duration'] = $row->program_duration;
            $rhett['enrollment'] = $this->cohort->getUserCountForCohort($row->cohort_id);
        }
        return $rhett;
    }

    /**
     * Retrieves program and cohort information for a given
     * learner group that is associated with that cohort/program.
     * @param int $groupId the group id
     * @return boolean|array an assoc. array with cohort/program info, or FALSE if none could be found
     * @todo move this method into a model
     * @todo optimize query
     */
    protected function _getProgramCohortStubByGroup ($groupId)
    {
        $rhett = false;
        $clean = array();
        $clean['group_id'] = (int) $groupId;

        $query =<<< EOL
SELECT
  `program`.`title` AS `program_title`,
  `program`.`short_title` AS `program_short_title`,
  `program`.`duration` AS `program_duration`,
  `cohort`.`cohort_id` AS `cohort_id`,
  `cohort`.`title` AS `cohort_title`,
  `cohort`.`program_year_id` AS `program_year_id`
FROM
  `program`, `program_year`, `cohort`, `cohort_master_group`,
  (SELECT root_group_of_group({$clean['group_id']}) AS `rgid`) AS `t1`
WHERE
  `cohort`.`program_year_id` = `program_year`.`program_year_id`
  AND `program`.`program_id` = `program_year`.`program_id`
  AND `cohort`.`cohort_id` = `cohort_master_group`.`cohort_id`
  AND `cohort_master_group`.`group_id` = `t1`.`rgid`
ORDER BY `program_title`, `cohort_title`
EOL;
        $DB = $this->dbHandle;
        $queryResults = $DB->query($query);

        if ($queryResults->num_rows() > 0) {

            $row = $queryResults->first_row();
            $rhett = array();
            $rhett['program_title'] = $row->program_title;
            $rhett['cohort_id'] = $row->cohort_id;
            $rhett['cohort_title'] = $row->cohort_title;
            $rhett['program_year_id'] = $row->program_year_id;
            $rhett['program_short_title'] = $row->program_short_title;
            $rhett['program_duration'] = $row->program_duration;
            $rhett['enrollment'] = $this->cohort->getUserCountForCohort($row->cohort_id);
        }
        return $rhett;
    }

    /**
     * @param groupModel assumed to contain an array associated to a key 'users' and have a value
     *                      associated to a key 'group_id'
     * @return true if the insert appears to have gone ok, false otherwise
     */
    protected function _associateUsersToGroup ($users, $groupId, &$auditAtoms)
    {
        $userIds = array();

        $len = count($users);
        for ($i = 0; $i < $len; $i++) {
            array_push($userIds, $users[$i]['user_id']);
        }

        return $this->group->makeUserGroupAssociations($userIds, $groupId, $auditAtoms);
    }

    /**
     * @todo add code docs
     */
    protected function _recursivelySaveGroupTree ($groupID, $title, $instructors, $location,
            $parentGroupId, $users, $subgroups, &$auditAtoms)
    {
        $rhett = array();

        $len = count($subgroups);
        for ($i = 0; (($i < $len) && (! isset($rhett['error']))); $i++) {
            $subgroup = $subgroups[$i];

            $result = $this->_recursivelySaveGroupTree(
                    $subgroup['group_id'],
                    $subgroup['title'],
                    $subgroup['instructors'],
                    $subgroup['location'],
                    $subgroup['parent_group_id'],
                    $subgroup['users'],
                    $subgroup['subgroups'],
                    $auditAtoms);

            if ($result != null) {
                $rhett['error'] = $result['error'];
            }
        }

        if (! isset($rhett['error'])) {
            $idArray = array();
            array_push($idArray, $groupID);

            $this->group->deleteUserGroupAssociationForGroupIds($idArray, $auditAtoms);

            $failed = $this->group->transactionAtomFailed();

            if (! $failed) {
                $result = $this->group->saveGroupForGroupId($groupID, $title, $instructors,
                    $location, $parentGroupId, $auditAtoms, ($parentGroupId != null));

                $failed = $this->group->transactionAtomFailed();
            }

            if ($failed || ($result != null)) {
                $rhett['error'] = ($result != null) ? $result : "There was a Database Deadlock error.";
            } else {
                if (! $this->_associateUsersToGroup($users, $groupID, $auditAtoms)) {
                    $rhett['error'] = 'A failure occurred making new user-group associations for group id ' . $groupID;
                }
            }
        }

        return $rhett;
    }

    /**
     * Retrieves user group trees for given groups.
     * @param array $groupIds a list of top-level group ids
     * @return array|boolean an array of groups, or FALSE on failure
     */
    protected function _getGroupsForGroupIds ($groupIds)
    {
        $rhett = array();

        foreach ($groupIds as $groupId) {
            $group = $this->group->getModelArrayForGroupId($groupId);

            if (is_null($group)) {
                return false;
            }

            $group['subgroups'] = $this->group->getSubgroupsForGroupId($groupId);
            $group['courses'] = $this->queries->getCourseIdAndTitleForLearnerGroup($groupId);
            $rhett[] = $group;
        }
        return $rhett;
    }

    /**
     * Recursively converts a given group tree with explicit membership representation into a group tree with implicit group membership.
     * The result is a group tree where each user exists only once - in the most distant subgroup relative to the given root.
     * This function's counterpart is <code>Group_Management::_backfillGroupMemberships</code>.
     * @param array $group a user group. Will be modified in-place.
     * @return array a list containing all ids of users that are members of the given group and its sub-groups.
     * @see Group_Management::_backfillGroupMemberships()
     */
    protected function _stripdownGroupMembership (array &$group)
    {
        // return $group;
        $memberIds = array();
        for ($i = 0, $n = count($group['subgroups']); $i < $n; $i++) {
            $subgroupMemberIds = $this->_stripdownGroupMembership($group['subgroups'][$i]);
            $memberIds = array_merge($memberIds, $subgroupMemberIds);
        }
        $users = array();
        for ($i = 0, $n = count($group['users']); $i < $n; $i++) {
            if (! in_array($group['users'][$i]->user_id, $memberIds)) {
                $users[] = $group['users'][$i];
                $memberIds[] = $group['users'][$i]->user_id;
            }
        }
        $group['users'] = $users;

        return $memberIds;
    }

    /**
     * Recursively converts a group tree from an implicit membership representation to a fully populated, explicit
     * membership  representation.
     * The result is a group tree where each user is represented explicitly in all groups and parent-groups
     * that it is a member of.
     * This function's counterpart is <code>Group_Management::_stripdownGroupMembership</code>.
     * @param array $group a the user group. Will be modified in-place.
     * @return array a list containing all users that are members of the given group and its sub-groups.
     * @see Group_Management::_backfillGroupMemberships::_stripdownGroupMembership()
     */
    protected function _backfillGroupMemberships (array &$group)
    {
        if (! array_key_exists('users', $group) || ! is_array($group['users'])) {
            $group['users'] = array();
        }

        if (! array_key_exists('subgroups', $group) || ! is_array($group['subgroups'])) {
            $group['subgroups'] = array();
        }

        for ($i = 0, $n = count($group['subgroups']); $i < $n; $i++) {
            $subgroupMembers = $this->_backfillGroupMemberships($group['subgroups'][$i]);
            $group['users'] = array_merge($group['users'], $subgroupMembers);
        }
        return $group['users'];
    }
}
