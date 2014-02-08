<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object to the "learning_materials" table in the Ilios database.
 */
class Learning_Material extends Ilios_Base_Model
{
    static public $LM_REPOSITORY_ROOT = '/learning_materials/'; // @todo make this configurable

    // constants
    /**
     * "Draft" status indicator.
     * @var int
     */
    const DRAFT_STATUS = 1;

    /**
     * "Final" status indicator.
     * @var int
     */
    const FINAL_STATUS = 2;

    /**
     * "Revised" status indicator.
     * @var int
     */
    const REVISED_STATUS = 3;

    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct('learning_material', array('learning_material_id'));
        $this->load->model('Mesh', 'mesh', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Retrieves the path, name and mimetype of a given learning material.
     * @param int $learningMaterialId the learning material id
     * @return array an array of three elements,
     *   [0] is the mime-type,
     *   [1] is the filename,
     *   [2] is the absolute file path
     */
    public function getAssetPathAndFilenameAndType ($learningMaterialId)
    {
        $rhett = array();

        $lmRow = $this->getRowForPrimaryKeyId($learningMaterialId);

        $rhett[] = $lmRow->mime_type;
        $rhett[] = $lmRow->filename;
        $rhett[] = getcwd() . $lmRow->relative_file_system_location;

        return $rhett;
    }

    /**
     * For any title, filename, and/or description which match the match string, return the row as a
     *  model in the returned array. Each model will contain 'learning_material_id', 'title',
     *  'filename', 'filesize', 'upload_date', 'copyright_ownership',
     *  'copyright_rationale', 'mime_type', 'owning_user_id' and 'owning_user_name'
     */
    public function getLearningMaterialsMatchingString ($matchString)
    {
        $rhett = array();
        $clean = array();

        $clean['search'] = $this->db->escape_like_str($matchString);

        $len = strlen($matchString);

        $sql = array();
        $sql[] =<<< EOL
SELECT
lm.`learning_material_id`,
lm.`title`,
lm.`mime_type`,
lm.`filename`,
lm.`filesize`,
lm.`description`,
lm.`learning_material_status_id` AS 'status_id',
lm.`learning_material_user_role_id` AS 'owner_role_id',
lm.`copyright_ownership`,
lm.`copyright_rationale`,
lm.`upload_date`,
lm.`owning_user_id`,
lm.`asset_creator`,
lm.`citation`,
lm.`web_link`,
CONCAT(COALESCE(u.`first_name`, ''), ' ', COALESCE(u.`last_name`, '')) AS 'owning_user_name'
FROM `learning_material` lm
JOIN `user` u ON u.`user_id` = lm.`owning_user_id`
WHERE
EOL;
        if (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
           $sql[] =<<< EOL
lm.`title` LIKE '{$clean['search']}%'
OR lm.`filename` LIKE '{$clean['search']}%'
OR lm.`description` LIKE '{$clean['search']}%'
EOL;
        } else {
            $sql[] =<<< EOL
lm.`title` LIKE '%{$clean['search']}%'
OR lm.`filename` LIKE '%{$clean['search']}%'
OR lm.`description` LIKE '%{$clean['search']}%'
EOL;
        }
        $sql[] =<<< EOL
ORDER BY lm.`title` ASC, lm.`learning_material_id`
EOL;
        $queryResults = $this->db->query(implode(' ', $sql));

        foreach ($queryResults->result_array() as $row) {
            $rhett[] = $row;
        }

        return $rhett;
    }

    /**
     * For all learning materials associated to the session, return them as models in an array. Each
     * model will contain 'learning_material_id', 'title', 'filename', 'filesize',
     * 'upload_date', 'copyright_ownership', 'copyright_rationale', 'mime_type', 'owning_user_id'
     * and 'owning_user_name'
     * @param int $sessionId the session identifier
     * @param boolean $excludeDrafts pass TRUE to exclude materials in "draft" status
     * @return array a nested array of associative arrays, each subarray representing a learning material record
     */
    public function getLearningMaterialsForSession ($sessionId, $excludeDrafts = false)
    {
        $rhett = array();

        $this->db->where('session_id', $sessionId);
        $queryResults = $this->db->get('session_learning_material');
        foreach ($queryResults->result_array() as $row) {
            $lmId = $row['learning_material_id'];
            $learningMaterialRow = $this->convertStdObjToArray($this->getRowForPrimaryKeyId($lmId));
            $canAdd = true;

            if ($excludeDrafts) {
                $canAdd = (Learning_Material::DRAFT_STATUS != (int) $learningMaterialRow['learning_material_status_id']);
            }

            if ($canAdd) {
                array_push($rhett, $this->makeReducedModelForRow($learningMaterialRow, $sessionId, false));
            }
        }

        return $rhett;
    }

    /**
     * For all learning materials associated to the course, return them as models in an array. Each
     * model will contain 'learning_material_id', 'title', 'filename', 'filesize',
     * 'upload_date', 'copyright_ownership', 'copyright_rationale', 'mime_type', 'owning_user_id'
     * and 'owning_user_name'
     * @param int $courseId the course identifier
     * @param boolean $excludeDrafts pass TRUE to exclude materials in "draft" status
     * @return array a nested array of associative arrays, each subarray representing a learning material record
     */
    public function getLearningMaterialsForCourse ($courseId, $excludeDrafts = false) {
        $rhett = array();

        $this->db->where('course_id', $courseId);
        $this->db->order_by('required', 'desc');

        $queryResults = $this->db->get('course_learning_material');
        foreach ($queryResults->result_array() as $row) {
            $lmId = $row['learning_material_id'];
            $learningMaterialRow = $this->convertStdObjToArray($this->getRowForPrimaryKeyId($lmId));
            $canAdd = true;

            if ($excludeDrafts) {
                $canAdd = (Learning_Material::DRAFT_STATUS != (int) $learningMaterialRow['learning_material_status_id']);
            }

            if ($canAdd) {
                array_push($rhett, $this->makeReducedModelForRow($learningMaterialRow, $courseId, true));
            }
        }

        return $rhett;
    }

    /**
     * Adds owning user data to a given learning material record, and,
     * if specified, additional associated session or course information.
     * @param array $row the learning material record
     * @param int|NULL $tableId either a course or session id, or NULL for none
     * @param boolean $isCourse TRUE for course, FALSE for session
     * @return array the extended LM data.
     * @todo get rid of this performance bottleneck, by breaking this out into individual queries using JOINs.
     */
    protected function makeReducedModelForRow ($row, $tableId, $isCourse)
    {
        $model = array();

        $model['learning_material_id'] = $row['learning_material_id'];
        $model['title'] = $row['title'];
        $model['mime_type'] = $row['mime_type'];
        $model['filename'] = $row['filename'];
        $model['filesize'] = $row['filesize'];
        $model['description'] = $row['description'];
        $model['status_id'] = $row['learning_material_status_id'];
        $model['owner_role_id'] = $row['learning_material_user_role_id'];
        $model['copyright_ownership'] = $row['copyright_ownership'];
        $model['copyright_rationale'] = $row['copyright_rationale'];
        $model['upload_date'] = $row['upload_date'];
        $model['owning_user_id'] = $row['owning_user_id'];
        $model['asset_creator'] = $row['asset_creator'];
        $model['citation'] = $row['citation'];
        $model['web_link'] = $row['web_link'];

        $userRow = $this->user->getRowForPrimaryKeyId($row['owning_user_id']);
        $model['owning_user_name'] = $userRow->first_name . ' ' . $userRow->last_name;

        if (! is_null($tableId)) {
            if ($isCourse) {
                $clmId = $this->getCourseLearningMaterialId($row['learning_material_id'], $tableId, null, false, false);

                if (! is_null($clmId)) {
                    $this->db->where('course_learning_material_id', $clmId);
                    $queryResults = $this->db->get('course_learning_material');
                    $model['notes'] = $queryResults->first_row()->notes;
                    $model['required'] = $queryResults->first_row()->required;
                    $model['notes_are_public'] = $queryResults->first_row()->notes_are_public;

                    $meshTerms = array();
                    $this->db->where('course_learning_material_id', $clmId);
                    $queryResults = $this->db->get('course_learning_material_x_mesh');
                    foreach ($queryResults->result_array() as $row) {
                        array_push($meshTerms,
                                   $this->mesh->getMeSHObjectForDescriptor($row['mesh_descriptor_uid']));
                    }
                    $model['mesh_terms'] = $meshTerms;
                }
            } else {
                $slmId = $this->_getSessionLearningMaterialId($tableId, $row['learning_material_id']);

                if (! is_null($slmId)) {
                    $this->db->where('session_learning_material_id', $slmId);
                    $queryResults = $this->db->get('session_learning_material');
                    $model['notes'] = $queryResults->first_row()->notes;
                    $model['required'] = $queryResults->first_row()->required;
                    $model['notes_are_public'] = $queryResults->first_row()->notes_are_public;

                    $meshTerms = array();
                    $this->db->where('session_learning_material_id', $slmId);
                    $queryResults = $this->db->get('session_learning_material_x_mesh');
                    foreach ($queryResults->result_array() as $row) {
                        array_push($meshTerms, $this->mesh->getMeSHObjectForDescriptor($row['mesh_descriptor_uid']));
                    }
                    $model['mesh_terms'] = $meshTerms;
                }
            }
        }
        return $model;
    }

    /**
     * @todo add docs
     * @return array
     */
    public function getLearningMaterialStatuses ()
    {
        $rhett = array();

        $queryResults = $this->db->get('learning_material_status');
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * @todo add docs
     * @return array
     */
    public function getLearningMaterialUserRoles ()
    {
        $rhett = array();

        $queryResults = $this->db->get('learning_material_user_role');
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * @todo add docs
     * @param int $learningMaterialId
     * @param int $courseId
     * @param string $notes
     * @param boolean $required
     * @param boolean $createUpdateIfNeeded
     * @param boolean $notesArePublicViewable
     * @return int|NULL
     */
    protected function getCourseLearningMaterialId ($learningMaterialId, $courseId, $notes,
                                                    $required, $createUpdateIfNeeded,
                                                    $notesArePublicViewable = true)
    {
        $rhett = null;

        $this->db->where('course_id', $courseId);
        $this->db->where('learning_material_id', $learningMaterialId);
        $queryResults = $this->db->get('course_learning_material');
        if ($queryResults->num_rows() > 0) {
            $rhett = $queryResults->first_row()->course_learning_material_id;

            if ($createUpdateIfNeeded) {
                $updatedRow = array();
                $updatedRow['notes'] = $notes;
                $updatedRow['required'] = $required ? 1 : 0;
                $updatedRow['notes_are_public'] = $notesArePublicViewable ? 1 : 0;

                $this->db->where('course_learning_material_id', $rhett);
                $this->db->update('course_learning_material', $updatedRow);
            }
        }
        else if ($createUpdateIfNeeded) {
            $newRow = array();
            $newRow['course_id'] = $courseId;
            $newRow['learning_material_id'] = $learningMaterialId;
            $newRow['notes'] = $notes;
            $newRow['required'] = $required ? 1 : 0;
            $newRow['notes_are_public'] = $notesArePublicViewable ? 1 : 0;

            $this->db->insert('course_learning_material', $newRow);

            $rhett = $this->db->insert_id();
        }

        return $rhett;
    }

    /**
     * Retrieves the session learning material id by a given session-id/learning-material-id pair.
     * @param int $sessionId
     * @param int $learningMaterialId
     * @return int|NULL the session learning material id, or NULL if none was found
     *
     */
    public function _getSessionLearningMaterialId ($sessionId, $learningMaterialId)
    {
        $this->db->where('session_id', $sessionId);
        $this->db->where('learning_material_id', $learningMaterialId);
        $queryResults = $this->db->get('session_learning_material');
        if ($queryResults->num_rows() > 0) {
            return $queryResults->first_row()->session_learning_material_id;
        }
        return null;
    }

    /**
     * @deprecated
     * use <code>Learning_Material::_getSessionLearningMaterialId()</code> instead.
     * @todo get rid of it
     * @see Learning_Material::_getSessionLearningMaterialId()
     * @param int $learningMaterialId
     * @param int $sessionId
     * @param string $notes
     * @param boolean $required
     * @param boolean $createUpdateIfNeeded
     * @param boolean $notesArePublicViewable
     * @return int|NULL
     */
    protected function getSessionLearningMaterialId ($learningMaterialId, $sessionId, $notes,
                                                     $required, $createUpdateIfNeeded,
                                                     $notesArePublicViewable = true)
    {
        $rhett = null;

        $this->db->where('session_id', $sessionId);
        $this->db->where('learning_material_id', $learningMaterialId);
        $queryResults = $this->db->get('session_learning_material');
        if ($queryResults->num_rows() > 0) {
            $rhett = $queryResults->first_row()->session_learning_material_id;

            if ($createUpdateIfNeeded) {
                $updatedRow = array();
                $updatedRow['notes'] = $notes;
                $updatedRow['required'] = $required ? 1 : 0;
                $updatedRow['notes_are_public'] = $notesArePublicViewable ? 1 : 0;

                $this->db->where('session_learning_material_id', $rhett);
                $this->db->update('session_learning_material', $updatedRow);
            }
        }
        else if ($createUpdateIfNeeded) {
            $newRow = array();
            $newRow['session_id'] = $sessionId;
            $newRow['learning_material_id'] = $learningMaterialId;
            $newRow['notes'] = $notes;
            $newRow['required'] = $required ? 1 : 0;
            $newRow['notes_are_public'] = $notesArePublicViewable ? 1 : 0;

            $this->db->insert('session_learning_material', $newRow);

            $rhett = $this->db->insert_id();
        }

        return $rhett;
    }

    /**
     * @todo add docs
     * @param int $learningMaterialId
     * @param int $dbId
     * @param boolean $isCourse
     * @param array $auditAtoms
     * @param array $meshTerms
     * @param string $notes
     * @param boolean $required
     * @param boolean $notesArePubliclyViewable
     * @return boolean
     */
    public function associateLearningMaterial ($learningMaterialId, $dbId, $isCourse, &$auditAtoms,
                                        $meshTerms = null, $notes = null, $required = true,
                                        $notesArePubliclyViewable = true)
    {
        //$this->disassociateLearningMaterial($learningMaterialId, $dbId, $isCourse, $auditAtoms, true);

        if ($isCourse) {
            $clmId = $this->getCourseLearningMaterialId($learningMaterialId, $dbId, $notes,
                                                        $required, true, $notesArePubliclyViewable);

            if (! is_null($meshTerms)) {
                $newRow = array();
                $newRow['course_learning_material_id'] = $clmId;

                foreach ($meshTerms as $meshTerm) {
                    $newRow['mesh_descriptor_uid'] = $meshTerm['dbId'];
                    $this->db->insert('course_learning_material_x_mesh', $newRow);

                    if ($this->transactionAtomFailed()) {
                        return false;
                    }

                    $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($clmId, 'course_learning_material_id',
                        'course_learning_material_x_mesh', Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
                }
            }
        }
        else {
            $slmId = $this->getSessionLearningMaterialId($learningMaterialId, $dbId, $notes,
                                                         $required, true,
                                                         $notesArePubliclyViewable);

            if (! is_null($meshTerms)) {
                $newRow = array();
                $newRow['session_learning_material_id'] = $slmId;

                foreach ($meshTerms as $meshTerm) {
                    $newRow['mesh_descriptor_uid'] = $meshTerm['dbId'];
                    $this->db->insert('session_learning_material_x_mesh', $newRow);

                    if ($this->transactionAtomFailed()) {
                        return false;
                    }

                    $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($slmId, 'session_learning_material_id',
                        'session_learning_material_x_mesh', Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
                }
            }
        }

        return ($this->db->affected_rows() != 0);
    }

    /**
     * @todo add docs
     * @param int $learningMaterialId
     * @param int $dbId
     * @param boolean $isCourse
     * @param array $auditAtoms
     * @param boolean $meshOnly
     * @return boolean
     */
    public function disassociateLearningMaterial ($learningMaterialId, $dbId, $isCourse, &$auditAtoms,
                                           $meshOnly = false)
    {
        if (! is_null($learningMaterialId)) {
            $this->db->where('learning_material_id', $learningMaterialId);
        }

        if ($isCourse) {
            $this->db->where('course_id', $dbId);
            $queryResults = $this->db->get('course_learning_material');
            foreach ($queryResults->result_array() as $row) {
                $this->db->where('course_learning_material_id', $row['course_learning_material_id']);
                $this->db->delete('course_learning_material_x_mesh');

                if ($this->transactionAtomFailed()) {
                    return false;
                }

                $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($row['course_learning_material_id'],
                    'course_learning_material_id', 'course_learning_material_x_mesh',
                    Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
            }

            if (! $meshOnly) {
                if (! is_null($learningMaterialId)) {
                    $this->db->where('learning_material_id', $learningMaterialId);
                }

                $this->db->where('course_id', $dbId);
                $this->db->delete('course_learning_material');

                if ($this->transactionAtomFailed()) {
                    return false;
                }

                $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($dbId, 'course_id', 'course_learning_material',
                    Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
            }
        }
        else {
            $this->db->where('session_id', $dbId);
            $queryResults = $this->db->get('session_learning_material');
            foreach ($queryResults->result_array() as $row) {
                $this->db->where('session_learning_material_id', $row['session_learning_material_id']);
                $this->db->delete('session_learning_material_x_mesh');

                if ($this->transactionAtomFailed()) {
                    return false;
                }

                $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($row['session_learning_material_id'],
                    'session_learning_material_id', 'session_learning_material_x_mesh',
                    Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
            }

            if (! $meshOnly) {
                if (! is_null($learningMaterialId)) {
                    $this->db->where('learning_material_id', $learningMaterialId);
                }

                $this->db->where('session_id', $dbId);
                $this->db->delete('session_learning_material');

                if ($this->transactionAtomFailed()) {
                    return false;
                }

                $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($dbId, 'session_id', 'session_learning_material',
                    Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
            }
        }

        return ($this->db->affected_rows() != 0);
    }

    /**
     * @todo add code docs
     * @param int $learningMaterialId
     * @param int $statusId
     * @param array $auditAtoms
     * @return string|NULL
     */
    public function modifyLearningMaterial ($learningMaterialId, $statusId, &$auditAtoms)
    {
        $rhett = null;

        $this->db->where('learning_material_id', $learningMaterialId);

        $updatedRow = array();
        $updatedRow['learning_material_status_id'] = $statusId;
        $this->db->update($this->databaseTableName, $updatedRow);

        $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($learningMaterialId, 'learning_material_id',
            $this->databaseTableName, Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE);

        if (($this->db->affected_rows() == 0) || $this->transactionAtomFailed()) {
            $msg = $this->languagemap->getI18NString('general.error.db_insert');

            $rhett = $msg;
        }

        return $rhett;
    }

    /*
     * Transactions assumed to be handled outside of this
     *
     * @return the learning material id, or -1 on failure
     */
    public function storeFileUploadLearningMaterialMeta ($title, $mimeType, $relativeAssetPath, $filename,
                                                  $filesize, $haveCopyrightOwnership,
                                                  $copyrightRationale, $description, $statusId,
                                                  $creator, $ownerRoleId, $courseId, $sessionId,
                                                  $userId, &$auditAtoms)
    {
        $newRow = array();
        $newRow['learning_material_id'] = null;

        $newRow['title'] = $title;
        $newRow['mime_type'] = $mimeType;
        $newRow['relative_file_system_location'] = $relativeAssetPath;
        $newRow['filename'] = $filename;
        $newRow['filesize'] = $filesize;

        $dtUpload = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['upload_date'] = $dtUpload->format('Y-m-d H:i:s');
        $newRow['owning_user_id'] = $userId;
        $newRow['asset_creator'] = $creator;
        $newRow['copyright_ownership'] = $haveCopyrightOwnership;
        $newRow['copyright_rationale'] = $copyrightRationale;

        $newRow['description'] = $description;
        $newRow['learning_material_status_id'] = $statusId;
        $newRow['learning_material_user_role_id'] = $ownerRoleId;

        $this->db->insert($this->databaseTableName, $newRow);
        $newId = $this->db->insert_id();

        if (($newId > 0) && (! $this->transactionAtomFailed())) {
            if ($sessionId == 0) {
                if (! $this->associateLearningMaterial($newId, $courseId, true, $auditAtoms)) {
                    return -1;
                }
            }
            else {
                if (! $this->associateLearningMaterial($newId, $sessionId, false, $auditAtoms)) {
                    return -1;
                }
            }

            $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newId, 'learning_material_id',
                $this->databaseTableName, Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
        }
        else {
            $newId = -1;
        }

        return $newId;
    }

    /*
     * Transactions assumed to be handled outside of this
     *
     * @return the learning material id, or -1 on failure
     */
    public function storeLinkLearningMaterialMeta ($title, $link, $description, $statusId, $creator,
                                            $ownerRoleId, $courseId, $sessionId, $userId, &$auditAtoms)
    {
        $newRow = array();
        $newRow['learning_material_id'] = null;

        $newRow['title'] = $title;
        $newRow['web_link'] = $link;
        $newRow['mime_type'] = 'link';
        $newRow['filesize'] = 0;

        $dtUpload = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['upload_date'] = $dtUpload->format('Y-m-d H:i:s');
        $newRow['owning_user_id'] = $userId;
        $newRow['asset_creator'] = $creator;
        $newRow['copyright_ownership'] = 2;
        $newRow['copyright_rationale'] = null;

        $newRow['description'] = $description;
        $newRow['learning_material_status_id'] = $statusId;
        $newRow['learning_material_user_role_id'] = $ownerRoleId;

        $this->db->insert($this->databaseTableName, $newRow);
        $newId = $this->db->insert_id();

        if (($newId > 0) && (! $this->transactionAtomFailed())) {
            if ($sessionId == 0) {
                if (! $this->associateLearningMaterial($newId, $courseId, true, $auditAtoms)) {
                    return - 1;
                }
            }
            else {
                if (! $this->associateLearningMaterial($newId, $sessionId, false, $auditAtoms)) {
                    return -1;
                }
            }

            $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newId, 'learning_material_id',
                $this->databaseTableName, Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
        }
        else {
            $newId = -1;
        }


        return $newId;
    }

    /*
     * Transactions assumed to be handled outside of this
     *
     * @return the learning material id, or -1 on failure
     */
    public function storeCitationLearningMaterialMeta ($title, $citation, $description, $statusId,
                                                $creator, $ownerRoleId, $courseId, $sessionId,
                                                $userId, &$auditAtoms)
    {
        $newRow = array();
        $newRow['learning_material_id'] = null;

        $newRow['title'] = $title;
        $newRow['citation'] = $citation;
        $newRow['mime_type'] = 'citation';
        $newRow['filesize'] = 0;

        $dtUpload = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['upload_date'] = $dtUpload->format('Y-m-d H:i:s');
        $newRow['owning_user_id'] = $userId;
        $newRow['asset_creator'] = $creator;
        $newRow['copyright_ownership'] = 2;
        $newRow['copyright_rationale'] = null;

        $newRow['description'] = $description;
        $newRow['learning_material_status_id'] = $statusId;
        $newRow['learning_material_user_role_id'] = $ownerRoleId;

        $this->db->insert($this->databaseTableName, $newRow);
        $newId = $this->db->insert_id();

        if (($newId > 0) && (! $this->transactionAtomFailed())) {
            if ($sessionId == 0) {
                if (! $this->associateLearningMaterial($newId, $courseId, true, $auditAtoms)) {
                    return -1;
                }
            }
            else {
                if (! $this->associateLearningMaterial($newId, $sessionId, false, $auditAtoms)) {
                    return -1;
                }
            }

            $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newId, 'learning_material_id',
                $this->databaseTableName, Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
        }
        else {
            $newId = -1;
        }

        return $newId;
    }

    /**
     * Adds/updates/deletes given session/learning material associations and associated meta-data.
     * @param int $sessionId
     * @param array $sessionLearningMaterials
     * @param array $associatedLearningMaterialIds
     * @param array $auditAtoms
     */
    public function saveSessionLearningMaterialAssociations ($sessionId, $sessionLearningMaterials = array(),
            $associatedLearningMaterialIds = array(), &$auditAtoms = array())
    {
        // figure out which associations need to be added, updated or removed.
        $keepAssocIds = array();
        $removeAssocIds = array();
        $addSessionLearningMaterials = array();
        $updateSessionLearningMaterials = array();
        if (! empty($associatedLearningMaterialIds)) {
            foreach ($sessionLearningMaterials as $item) {
                if (in_array($item['dbId'], $associatedLearningMaterialIds)) { // exists?
                    $keepAssocIds[] = $item['dbId']; // flag as "to keep"
                    $updateSessionLearningMaterials[] = $item;
                } else {
                    $addSessionLearningMaterials[] = $item; // mark as to add
                }
            }
            $removeAssocIds = array_diff($associatedLearningMaterialIds, $keepAssocIds); // find the assoc. to remove
        } else {
            $addSessionLearningMaterials = $sessionLearningMaterials; // mark all as "to be added"
        }

        if (count($addSessionLearningMaterials)) { // add learning materials to session
            $this->_addSessionLearningMaterialAssociations($sessionId, $sessionLearningMaterials, $auditAtoms);
        }
        if (count($updateSessionLearningMaterials)) { // update session/learning materials assoc.
            $this->_updateSessionLearningMaterialAssociations($sessionId, $updateSessionLearningMaterials, $auditAtoms);
        }
        if (count($removeAssocIds)) { // remove learning materials from session
            $this->_deleteSessionLearningMaterialAssociations($sessionId, $removeAssocIds, $auditAtoms);
        }
    }

    /**
     * Adds given session/learning materials associations.
     * @param int $sessionId
     * @param array $sessionLearningMaterials
     * @param array $auditAtoms
     */
    protected function _addSessionLearningMaterialAssociations ( $sessionId,
            $sessionLearningMaterials = array(), &$auditAtoms = array())
    {
        $lmiCache = array();
        foreach ($sessionLearningMaterials as $material) {
            // SANITY CHECK
            // prevent the same learning material from
            // being associated with the given session twice
            if (in_array($material['dbId'], $lmiCache)) {
                continue;
            }

            $row = array();
            $row['session_id'] = $sessionId;
            $row['learning_material_id'] = $material['dbId'];
            $row['notes'] = $material['notes'];
            $row['required'] = (int ) $material['required'];
            $row['notes_are_public'] = (int) $material['notesArePubliclyViewable'];
            $this->db->insert('session_learning_material', $row);

            // @todo add error handling
            $sessionLearningMaterialId = $this->db->insert_id();

            $lmiCache[] = $material['dbId'];

            // add mesh term associations
            if ($sessionLearningMaterialId && ! empty($material['meshTerms'])) {
                $this->_saveSessionLearningMaterialMeshTermAssociations($sessionLearningMaterialId,
                        $material['meshTerms'], array(), $auditAtoms);
            }
        }
    }

    /**
     * Removes associations between a given session and given learning materials.
     * @param int $sessionId
     * @param array $learningMaterialIds
     * @param array $auditAtoms
     * @todo implement audit trail
     */
    protected function _deleteSessionLearningMaterialAssociations ($sessionId,
            $learningMaterialIds = array(), &$auditAtoms = array())
    {
        $this->_disassociateFromJoinTable('session_learning_material', 'session_id',
                $sessionId, 'learning_material_id', $learningMaterialIds);
    }

    /**
     * Updates given session/learning materials associations.
     * @param array $sessionLearningMaterials
     * @param array $auditAtoms
     * @todo implement audit trail
     */
    protected function _updateSessionLearningMaterialAssociations ( $sessionId,
            $sessionLearningMaterials = array(), &$auditAtoms = array())
    {
        foreach ($sessionLearningMaterials as $material) {
            // retrieve the identifier of the given session learning material
            $sessionLearningMaterialId = $this->_getSessionLearningMaterialId($sessionId, $material['dbId']);
            // if no session learning material id was found, then we are essentially hosed.
            // this should have been caught further upstream.
            // for now, we just ignore this record and move on.
            // @todo implement better exception handling
            if (empty($sessionLearningMaterialId)) {
                continue;
            }

            $row = array();
            $row['notes'] = $material['notes'];
            $row['required'] = $material['required'] ? 1 : 0;
            $row['notes_are_public'] = $material['notesArePubliclyViewable'] ? 1 : 0;

            $this->db->where('session_learning_material_id', $sessionLearningMaterialId);
            $this->db->update('session_learning_material', $row);

            // update mesh term associations
            if (! empty($material['meshTerms'])) {
                $associatedMeshTermIds = $this->getIdArrayFromCrossTable('session_learning_material_x_mesh',
                        'mesh_descriptor_uid', 'session_learning_material_id', $sessionLearningMaterialId);
                $this->_saveSessionLearningMaterialMeshTermAssociations($sessionLearningMaterialId,
                        $material['meshTerms'], $associatedMeshTermIds, $auditAtoms);
            }
        }
    }

    /**
     * Saves the session-learning-material/mesh-term associations for a given session learning material
     * and given mesh terms, taken given pre-existings associations into account.
     *
     * @param int $sessionId the session id
     * @param array $meshTerms nested array of mesh terms
     * @param array|NULL $associatedMeshTermIds ids of mesh terms already associated with the given session
     * @param array $auditAtoms audit trail
     */
    protected function _saveSessionLearningMaterialMeshTermAssociations (
            $sessionLearningMaterialId, $meshTerms = array(), $associatedMeshTermIds = array(),
            array &$auditAtoms = array())
    {
        $this->_saveJoinTableAssociations('session_learning_material_x_mesh',
                'session_learning_material_id', $sessionLearningMaterialId,
                'mesh_descriptor_uid', $meshTerms, $associatedMeshTermIds, 'dbId', $auditAtoms);
    }
}
