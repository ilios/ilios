<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Learning materials management controller.
 */
class Learning_Materials extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Alert', 'alert', TRUE);
        $this->load->model('School', 'school', TRUE);
    }

    /**
     * Default action.
     */
    public function index ()
    {
        // not implemented
    }

    /**
     * Expected params:
     *      . learning_material_id
     *
     * @return a stream with the appropriate content type set
     */
    public function getLearningMaterialWithId ()
    {
        $rhett = array();

        // not extra authorization check here, learning materials are readable by all logged in users.
        $learningMaterialId = $this->input->get_post('learning_material_id');
        $rhett = $this->learningMaterial->getAssetPathAndFilenameAndType($learningMaterialId);

        //we have the filepath at this point ($rhett[2]), so let's make sure it's there...
        if(is_file($rhett[2])){
          //if the file exists, start streaming!
          header("Content-Type: " . $rhett[0]);
          header('Content-Disposition: attachment; filename="' . $rhett[1] . '"');
          $this->streamFileContentsChunked($rhett[2], false);
        } else {
          //otherwise, the file isn't where is should be, so throw a 404 with the filename ($rhett[1])
          show_error("Learning Material '" . $rhett[1] . "' was not found.", 404);
        }
    }

    /**
     * Expected params:
     *      . learning_material_id
     *      . status_id
     *
     * @return a JSON'd array with the key 'learning_material_id' and either the key 'error' or
     *              the key 'status_id'
     */
    public function modifyLearningMaterial ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $learningMaterialId = $this->input->get_post('learning_material_id');
        $statusId = $this->input->get_post('status_id');

        $rhett['learning_material_id'] = $learningMaterialId;

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->learningMaterial->startTransaction();

            $error = $this->learningMaterial->modifyLearningMaterial($learningMaterialId, $statusId,
                                                                     $auditAtoms);
            if (is_null($error)) {
                $rhett['status_id'] = $statusId;

                $failedTransaction = false;

                $this->learningMaterial->commitTransaction();

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
                $rhett['error'] = $error;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->learningMaterial);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      . search_string
     *
     * @return a JSON'd array of 0 or more models; each model will contain 'learning_material_id',
     *  'title', 'filename', 'filesize', 'upload_date', 'copyright_ownership',
     *  'copyright_rationale', 'mime_type', 'owning_user_id' and 'owning_user_name'. The
     *  learning_material_id value should be used when requesting a specific asset via this
     *  controller's getLearningMaterialWithId method.
     */
    public function getLearningMaterialDescriptorsForSearch ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $searchString = $this->input->get_post('search_string');
        if ('' !== trim($searchString)) {
            $rhett = $this->learningMaterial->getLearningMaterialsMatchingString($searchString);
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Associates learning materials with a session or course, based on posted user input.
     * Expected params:
     *      . session_id or course_id
     *      . learning_material_id
     *
     * Prints a JSON-formatted array with the key 'learning_material_id' and potentially also the key
     *              'error'
     * @todo improve code docs
     */
    public function associateLearningMaterial ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $learningMaterialId = $this->input->get_post('learning_material_id');
        $sessionId = $this->input->get_post('session_id');
        $courseId = $this->input->get_post('course_id');


        $school = false;
        if ($sessionId) {
            $school = $this->school->getSchoolBySessionId($sessionId);
        } elseif ($courseId) {
            $school = $this->school->getSchoolByCourseId($courseId);
        }
        // check if session or course are linked to a school
        // if this is not the case then echo out an error message
        // and be done with it.
        if (empty($school)) {
            $msg = $this->languagemap->getI18NString('learning_material.error.associate');
            $rhett = array();
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $userId = $this->session->userdata('uid');

        $rhett['learning_material_id'] = $learningMaterialId;

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->learningMaterial->startTransaction();

            $success = false;
            if (! $sessionId) {
                $success = $this->learningMaterial->associateLearningMaterial($learningMaterialId,
                    $courseId, true, $auditAtoms);
            } else {
                $success = $this->learningMaterial->associateLearningMaterial($learningMaterialId,
                    $sessionId, false, $auditAtoms);
            }

            if (! $success) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');
                $rhett['error'] = $msg;
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->learningMaterial);
            } else {
                $failedTransaction = false;
                $this->learningMaterial->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }

                // save change alerts
                $alertChangeTypes = array(Alert::CHANGE_TYPE_LEARNING_MATERIAL);
                $this->auditAtom->startTransaction();
                $success = false;
                if (! $sessionId) {
                    if ($this->course->isPublished($courseId)) {
                        $sessions = $this->iliosSession->getSimplifiedSessionsForCourse($courseId);
                        foreach ($sessions as $session) {
                            $sessionId = $session['session_id'];
                            $success = $this->_alertAllOfferingsAsAppropriate($sessionId, $courseId, 'course', $alertChangeTypes, $school);
                            if (! $success) {
                                break;
                            }
                        }
                    }
                } else {
                    $success = $this->_alertAllOfferingsAsAppropriate($sessionId, $sessionId, 'session', $alertChangeTypes, $school);
                }
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      . session_id or course_id
     *      . learning_material_id
     *
     * @return a JSON'd array with the key 'learning_material_id' and potentially also the key
     *              'error'
     */
    public function disassociateLearningMaterial ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $learningMaterialId = $this->input->get_post('learning_material_id');
        $sessionId = $this->input->get_post('session_id');
        $courseId = $this->input->get_post('course_id');


        $school = false;
        if ($sessionId) {
            $school = $this->school->getSchoolBySessionId($sessionId);
        } elseif ($courseId) {
            $school = $this->school->getSchoolByCourseId($courseId);
        }
        // check if session or course are linked to a school
        // if this is not the case then echo out an error message
        // and be done with it.
        if (empty($school)) {
            $msg = $this->languagemap->getI18NString('learning_material.error.associate');
            $rhett = array();
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $rhett['learning_material_id'] = $learningMaterialId;

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->learningMaterial->startTransaction();

            $success = false;

            if (! $sessionId) {
                $success = $this->learningMaterial->disassociateLearningMaterial($learningMaterialId,
                                                                                 $courseId,
                                                                                 true, $auditAtoms);
            } else {
                $success = $this->learningMaterial->disassociateLearningMaterial($learningMaterialId,
                                                                                 $sessionId,
                                                                                 false, $auditAtoms);
            }

            if (! $success) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');
                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->learningMaterial);
            } else {
                $failedTransaction = false;
                $this->learningMaterial->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }

                // save change alerts
                $alertChangeTypes = array(Alert::CHANGE_TYPE_LEARNING_MATERIAL);
                $this->auditAtom->startTransaction();
                $success = false;
                if (! $sessionId) {
                    if ($this->course->isPublished($courseId)) {
                        $sessions = $this->iliosSession->getSimplifiedSessionsForCourse($courseId);
                        foreach ($sessions as $session) {
                            $sessionId = $session['session_id'];

                            $success = $this->_alertAllOfferingsAsAppropriate($sessionId, $courseId, 'course',
                                    $alertChangeTypes, $school);
                            if (! $success) {
                                break;
                            }
                        }
                    }
                } else {
                    $success = $this->_alertAllOfferingsAsAppropriate($sessionId, $sessionId, 'session',
                            $alertChangeTypes, $school);
                }
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params, besides the one's generated by the upload form generation:
     *  . course_id
     *  . session_id
     *  . mime_type
     *  . title title for the learning item
     *
     * TODO doesn't handle updated versions of previously existing assets
     */
    public function uploadLearningMaterial ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        /*
         *  uploadPath should be:
         *    learningMaterials->getRoot()/course_id/session_id/
         */
        $courseId = (int) $this->input->post('course_id');
        $sessionId = (int) $this->input->post('session_id');

        if (0 > $courseId) {
            $msg = $this->languagemap->getI18NString('general.error.upload_fail');
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        if (0 > $sessionId) {
            $msg = $this->languagemap->getI18NString('general.error.upload_fail');
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $uploadPath = Learning_Material::$LM_REPOSITORY_ROOT . $courseId . '/' . $sessionId . '/';

        $absolutePath = getcwd() . $uploadPath;
        if (! is_dir($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        $config['upload_path'] = '.' . $uploadPath;
        // nightmarish
        $config['allowed_types'] = 'aiff|avi|csv|doc|docm|docx|dot|dotm|dotx|gif|gz|html|jpeg'
            . '|jpg|mov|mp3|mp4|mpg|pdf|png|pot|potm|potx|ppa|ppam|pps|ppsm|ppsx|ppt|pptm|pptx'
            . '|rtf|swf|tar|tiff|txt|wav|word|wmv|xla|xlam|xls|xlsb|xlsm|xlsx|xlt|xltm|xltx|xml|zip';
        $config['max_size'] = '107520'; // 105 MB -- todo this also need php.ini to say such a big thing is ok

        $this->load->library('upload', $config);

        $mimeType = 'not known yet';
        $filesize = 'not known yet';
        $filename = null;
        $copyrightRationale = null;

        $displayedTab = $this->input->get_post('displayed_tab');

        if (($displayedTab == 1) && (! $this->upload->do_upload())) {
            $msg = $this->languagemap->getI18NString('general.error.upload_fail');
            $uploadData = $this->upload->data();

            $mimeType = $uploadData['file_type'];

            $rhett['error'] = $msg . ': ' . $this->upload->display_errors() . ' Determined mime-type is [' . $mimeType . ']';
        } else {
            $clean = array();
            $names = array('title', 'description', 'content_creator', 'copyright_rationale');
            foreach ($names as $name) {
                $input = $this->input->post($name);
                $input = Ilios_CharEncoding::utf8UrlDecode($input);
                $clean[$name] = $input;
            }

            $title = $clean['title'];
            $description = $clean['description'];
            $copyrightRationale = $clean['copyright_rationale'];
            $creator = $clean['content_creator'];
            $ownerRoleId = $this->input->post('owner_role');
            $statusId = $this->input->post('status');

            switch ($displayedTab) {
                case 1:
                    $uploadData = $this->upload->data();
                    $mimeType = $this->upload->getCorrectedMimeType($uploadData['file_ext'], $uploadData['file_type']);
                    $filesize = round($uploadData['file_size']);
                    $uploadedFileName = $uploadData['file_name'];
                    $filename = $uploadData['orig_name'];
                    $haveCopyrightOwnership = $copyrightRationale ? 0 : 1;
                    break;
                case 2:
                    $mimeType = "link";
                    $filesize = 0;
                    $filename = null;
                    $haveCopyrightOwnership = 2;
                    break;
                case 3:
                    $mimeType = "citation";
                    $filesize = 0;
                    $filename = null;
                    $haveCopyrightOwnership = 2;
                    break;
                default:
                    // do nothing
            }

            $uniqueFilename = null;
            if (! is_null($filename)) {
                $uniqueFilename = $this->_moveFileToUniqueName($uploadPath, $uploadedFileName);

                if (is_null($uniqueFilename)) {
                    $rhett['error'] = $this->languagemap->getI18NString('general.error.file_rename');
                }
            }


            if (! isset($rhett['error'])) {
                $failedTransaction = true;
                $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
                do {
                    $auditAtoms = array();

                    unset($rhett['error']);

                    $this->learningMaterial->startTransaction();

                    $newLearningMaterialId = -1;
                    switch ($displayedTab) {
                        case 1:
                            $uploadFilePath = $uploadPath . $uniqueFilename;

                            $newLearningMaterialId = $this->learningMaterial->storeFileUploadLearningMaterialMeta(
                                $title, $mimeType, $uploadFilePath, $filename, $filesize, $haveCopyrightOwnership,
                                $copyrightRationale, $description, $statusId, $creator, $ownerRoleId, $courseId,
                                $sessionId, $userId, $auditAtoms);
                            break;
                        case 2:
                            $link = $this->input->get_post('web_link');

                            $newLearningMaterialId = $this->learningMaterial->storeLinkLearningMaterialMeta(
                                $title, $link, $description, $statusId, $creator, $ownerRoleId, $courseId,
                                $sessionId, $userId, $auditAtoms);
                            $rhett['web_link'] = $link;
                            break;
                        case 3:
                            $citation = $this->input->get_post('citation');
                            $newLearningMaterialId = $this->learningMaterial->storeCitationLearningMaterialMeta(
                                $title, $citation, $description, $statusId, $creator, $ownerRoleId, $courseId,
                                $sessionId, $userId, $auditAtoms);
                            $rhett['citation'] = $citation;
                            break;
                        default:
                            // do nothing
                    }

                    if (is_null($newLearningMaterialId) || ($newLearningMaterialId < 1)) {
                        $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');
                        Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->learningMaterial);
                    } else {
                        $failedTransaction = false;

                        $this->learningMaterial->commitTransaction();

                        // save audit trail
                        $this->auditAtom->startTransaction();
                        $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                        if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                            $this->auditAtom->rollbackTransaction();
                        } else {
                            $this->auditAtom->commitTransaction();
                        }

                        $rhett['learning_material_id'] = $newLearningMaterialId;
                    }
                } while ($failedTransaction && ($transactionRetryCount > 0));
            }
        }

        if (! isset($rhett['error'])) {
            $rhett['mime_type'] = $mimeType;
            $rhett['cid'] = $courseId;
            $rhett['sid'] = $sessionId;
            $rhett['upl'] = $uploadPath;
            $rhett['title'] = $this->input->post('title');
            $rhett['filename'] = $filename;
            $rhett['filesize'] = $filesize;
            $rhett['upload_date'] = $this->learningMaterial->getRowForPrimaryKeyId($newLearningMaterialId)->upload_date;
            $rhett['description'] = $this->input->post('description');
            $rhett['status_id'] = $this->input->post('status');
            $rhett['owner_role_id'] = $this->input->post('owner_role');
            $rhett['asset_creator'] = $this->input->post('content_creator');
            if ($copyrightRationale != null) {
                $rhett['copyright_rationale'] = $copyrightRationale;
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Renames a given file to a generated "unique" name.
     * @param string $originalRelativePath path to the file
     * @param string $originalFilename file name
     * @return string|NULL the new file name on success, or NULL on failure
     */
    protected function _moveFileToUniqueName ($originalRelativePath, $originalFilename)
    {
        $newFileName = date('Ymd-His') . '_' . rand(100, 999) . '_' . md5($originalFilename);

        $oldFilePath = getcwd() . $originalRelativePath . $originalFilename;
        $newFilePath = getcwd() . $originalRelativePath . $newFileName;

        if (rename($oldFilePath, $newFilePath)) {
            return $newFileName;
        }

        return null;
    }

    /**
     * @todo add code docs
     * @param int $sessionId
     * @param int $tableId
     * @param string $tableName
     * @param array $alertChangeTypes
     * @param array $school
     * @return boolean TRUE on success, FALSE on failure.
     */
    protected function _alertAllOfferingsAsAppropriate ($sessionId, $tableId, $tableName, $alertChangeTypes, $school)
    {
        $userId = $this->session->userdata('uid');

        $rhett = true;

        if ($this->iliosSession->isPublished($sessionId)) {
            $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($sessionId);

            if ($this->course->isPublished($sessionRow->course_id)) {
                $msg = $this->alert->addOrUpdateAlert($tableId, $tableName, $userId, $school, $alertChangeTypes);
                $rhett = is_null($msg);
            }
        }

        return $rhett;
    }
}
