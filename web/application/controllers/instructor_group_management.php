<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Instructor-group management controller.
 */
class Instructor_Group_Management extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
        $this->load->model('School', 'school', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Required POST or GET parameters:
     *      sid                 (school id)
     */
    public function index ()
    {
        $data = array();

        if (! $this->session->userdata('has_instructor_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $this->output->set_header('Expires: 0');

        $schoolId = $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if ($schoolRow != null) {
            $data['school_id'] = $schoolId;
            $data['school_name'] = $schoolRow->title;

            $data['viewbar_title'] = $this->config->item('ilios_institution_name');

            if ($schoolRow->title != null) {
                $key = 'general.phrases.school_of';
                $schoolOfStr = $this->languagemap->getI18NString($key);
                $data['viewbar_title'] .= ' ' . $schoolOfStr . ' ' . $schoolRow->title;
            }

            $groups = $this->_getGroups($schoolId);
            $data['groups_json'] = Ilios_Json::encodeForJavascriptEmbedding($groups['groups'],
                Ilios_Json::JSON_ENC_SINGLE_QUOTES);

            $key = 'instructor_groups.page_header';
            $data['page_header_string'] = $this->languagemap->getI18NString($key);

            $key = 'instructor_groups.title_bar';
            $data['title_bar_string'] = $this->languagemap->getI18NString($key);

            $this->load->view('instructor/instructor_group_manager', $data);
        } else {
            // error condition - todo
        }
    }

    public function getAssociatedCourses ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $igId = $this->input->get_post('instructor_group_id');

        $rhett['courses'] = $this->queries->getAssociatedCoursesForInstructorGroup($igId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }



    /**
     * This is part 1 of the upload transaction for adding new users via a CSV file. Part 2 is
     *  processUploadedInstructorListCSVFile. We need to break this into 2 pieces because YUI's
     *  asynchronous file upload response handler receives back a pile of HTML representing
     *  the contents of the uploaded file (there _MUST_ be some way to disable this, though
     *  i've not yet discovered it). Since we can't return a result block per our normal MO, we
     *  use the useless HTML-ful upload response handler to kick off the 2nd part of the upload
     *  transaction.
     *
     * This will insert all the users in the CSV file into the db in the faculty role associated to
     * the specified cohort. The columns, in order, expected in the CSV file are:
     *     Last name
     *     First name
     *     Middle name
     *     EMail address
     *     Phone
     *     UC id
     *     GALEN id
     *     Other id
     */
    public function uploadInstructorListCSVFile ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $uploadPath = './tmp_uploads/'; // @todo make this configurable

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '5000'; // 5000 KB

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload()) {
            $msg = $this->languagemap->getI18NString('general.error.upload_fail');
            $msg2 = $this->languagemap->getI18NString('general.phrases.found_mime_type');
            $uploadData = $this->upload->data();

            $rhett['error'] = $msg . ': ' . $this->upload->display_errors() . '. ' . $msg2 . ': ' . $uploadData['file_type'];
        } else {
            $uploadData = $this->upload->data();
            $groupId = $this->input->post('instructor_group_id');
            $containerNumber = $this->input->post('container_number');

            $newIds = array();
            $newUsers = array();

            $this->load->library('csvreader');

            // false parameter => no named fields on line 0 of the csv
            $csvData = $this->csvreader->parse_file(($uploadData['full_path']), false);

            $foundDuplicates = array();

            foreach ($csvData as $row) {
                $email = $row[4];

                if ($this->user->userExistsWithEmail($email)) {
                    array_push($foundDuplicates, ($email . ' ' . $row[0] . ', ' . $row[1] . ' ' . $row[2]));
                }
            }

            // MAY RETURN THIS BLOCK
            if (count($foundDuplicates) > 0) {
                $msg = $this->languagemap->getI18NString('general.error.duplicate_users_found');

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
            $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
            do {
                $auditAtoms = array();

                unset($rhett['error']);

                $this->instructorGroup->startTransaction();

                foreach ($csvData as $row) {
                    $lastName = trim($row[0]);
                    $firstName = trim($row[1]);
                    $middleName = trim($row[2]);
                    $phone = trim($row[3]);
                    $email = trim($row[4]);
                    $ucUID = trim($row[5]);
                    $otherId = trim($row[7]);

                    $primarySchoolId = $this->session->userdata('school_id');

                    $newId = $this->user->addUserAsFaculty($lastName, $firstName, $middleName, $phone,
                        $email, $ucUID, $otherId, $primarySchoolId, $auditAtoms);

                    if (($newId <= 0) || $this->user->transactionAtomFailed()) {
                        $msg = $this->languagemap->getI18NString('general.error.db_insert');
                        $rhett['error'] = $msg;
                        break;
                    }

                    $newIds[] = $newId;
                    $newUsers[] = $this->convertStdObjToArray($this->user->getRowForPrimaryKeyId($newId));
                }

                if (isset($rhett['error'])
                    || (! $this->instructorGroup->makeUserGroupAssociations($newIds, $groupId, $auditAtoms))) {
                    if (! isset($rhett['error'])) {
                        $rhett['error'] = "There was a database deadlock exception.";
                    }

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->instructorGroup);

                } else {
                    $rhett['container_number'] = $containerNumber;
                    $rhett['users'] = $newUsers;

                    $failedTransaction = false;

                    $this->instructorGroup->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));

            if (! unlink($uploadData['full_path'])) {
                log_message('warning', 'Was unable to delete uploaded IGM CSV file: '. $uploadData['orig_name']);
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Called via the Edit Members (or whatever) dialog for the db addition of a new user (given
     *  an instructor_group_id) -- entries in the tables user and instructor_group_x_user are made.
     *
     * @return a json'd array with either the key 'error', or the key pair 'user' and
     *              'container_number' (the latter being a passback from the incoming param)
     */
    public function addNewUserToGroup ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $groupId = $this->input->get_post('instructor_group_id');
        $containerNumber = $this->input->get_post('container_number');
        $lastName = trim($this->input->get_post('last_name'));
        $firstName = trim($this->input->get_post('first_name'));
        $middleName = trim($this->input->get_post('middle_name'));
        $phone = trim($this->input->get_post('phone'));
        $email = trim($this->input->get_post('email'));
        $ucUID = trim($this->input->get_post('uc_uid'));

        // MAY RETURN THIS BLOCK
        if ($this->user->userExistsWithEmail($email)) {
            $msg = $this->languagemap->getI18NString('general.error.duplicate_user_found');

            $rhett['error'] = $msg;

            header("Content-Type: text/plain");
            echo json_encode($rhett);

            return;
        }

        $userId = $this->session->userdata('uid');

        $primarySchoolId = $this->session->userdata('school_id');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->instructorGroup->startTransaction();

            $newId = $this->user->addUserAsFaculty($lastName, $firstName, $middleName, $phone,
                                                   $email, $ucUID, '', $primarySchoolId,
                                                   $auditAtoms);

            if (($newId <= 0) || $this->user->transactionAtomFailed()) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');

                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->instructorGroup);
            }
            else {
                $userIds = array();
                array_push($userIds, $newId);

                if (! $this->instructorGroup->makeUserGroupAssociations($userIds, $groupId,
                                                                        $auditAtoms)) {
                    $msg = $this->languagemap->getI18NString('general.error.db_insert');

                    $rhett['error'] = $msg;

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                           $this->instructorGroup);
                }
                else {
                    $failedTransaction = false;

                    $this->instructorGroup->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    $rhett['container_number'] = $containerNumber;
                    $rhett['user'] = $this->user->getRowForPrimaryKeyId($newId);
                }
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /*
     * Expected parameters:
     *  . 'next_container'
     *
     * @return a json'd array with either the key 'error', or the keys group_id, title, and
     *              container_number (which is a passthrough of next_container)
     */
    public function addNewEmptyGroup ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');
        $schoolId = $this->session->userdata('school_id');

        $containerNumber = $this->input->get_post('next_container');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            $this->instructorGroup->startTransaction();

            $rhett = $this->instructorGroup->addNewEmptyGroup($containerNumber, $schoolId,
                                                              $auditAtoms);

            if (! isset($rhett['error'])) {
                $rhett['container_number'] = $containerNumber;

                $failedTransaction = false;

                $this->instructorGroup->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
            else {
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->instructorGroup);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      'instructor_group_id'
     *      'container_number'
     *
     * @return a json'd array with keys 'instructor_group_id' and 'container_number', or 'error'
     */
    public function deleteGroup ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $groupId = $this->input->get_post('instructor_group_id');
        $containerNumber = $this->input->get_post('container_number');

        // check if the given instructor group is associated with a locked or archived course in
        // any way (e.g. via an offering or independent learning session)
        // if this is the case then this instructor group must be considered "locked down".
        // we reject the deletion request and return an error message stating just that.
        if ($this->instructorGroup->isAssociatedWithLockedAndArchivedCourses($groupId)) {
            $msg = $this->languagemap->getI18NString('instructor_groups.error.group_deletion.locked_course');
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->instructorGroup->startTransaction();

            if ($this->instructorGroup->deleteGroupWithInstructorGroupId($groupId, $auditAtoms)
                && (! $this->instructorGroup->transactionAtomFailed())) {

                $rhett['instructor_group_id'] = $groupId;
                $rhett['container_number'] = $containerNumber;

                $failedTransaction = false;

                $this->instructorGroup->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            } else {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.fatal');
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->instructorGroup);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      'instructor_group_id'
     *      'container_number'
     *      'title'
     *      'users' - an array of valid user_id values
     *
     * TODO transaction needed after moving to InnoDB
     *
     * @return a json'd array with key 'instructor_group_id' on success, or 'error' on failure
     */
    public function saveGroup ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $groupId = $this->input->get_post('instructor_group_id');
        $schoolId = $this->session->userdata('school_id');
        $containerNumber = $this->input->get_post('container_number');
        $title = rawurldecode($this->input->get_post('title'));
        $users = json_decode($this->input->get_post('users'), true);

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            $rhett = array();

            $this->instructorGroup->startTransaction();

            $result = $this->instructorGroup->saveGroup($groupId, $schoolId, $title, $users,
                                                        $auditAtoms);

            if (! isset($result)) {
                $rhett['instructor_group_id'] = $groupId;
                $rhett['container_number'] = $containerNumber;

                $failedTransaction = false;

                $this->instructorGroup->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
            else {
                $rhett['error'] = $result;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->instructorGroup);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Required parameter:
     *  . school_id     if the method parameter is undefined, expected via GET or POST
     */
    protected function _getGroups ($schoolId = null)
    {
        $rhett = array();

        if ($schoolId == null) {
            $schoolId = $this->input->get_post('school_id');
        }

        $groups = $this->instructorGroup->getModelArrayForSchoolId($schoolId);

        // todo error conditions

        $rhett['groups'] = $groups;
        return $rhett;
    }
}
