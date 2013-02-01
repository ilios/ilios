<?php
include_once "abstract_ilios_model.php";

/**
 * Data Access Object to the "instructor_group" table in the Ilios database.
 */
class Instructor_Group extends Abstract_Ilios_Model
{

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('instructor_group', array('instructor_group_id'));

        $this->createDBHandle();

        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Checks if a given instructor group is associated with any locked and archived courses.
     * Note: "deleted" courses (and assoc. sessions) will be ignored, regardless of their archiving status.
     * @param int $groupId the group identifier
     * @return boolean TRUE if at least one association to an archived course exists, FALSE otherwise
     */
    public function isAssociatedWithLockedAndArchivedCourses ($groupId)
    {
        $rhett = false;
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<<EOL
SELECT DISTINCT c.`course_id`
FROM `course` c
JOIN `session` s ON s.`course_id` = c.`course_id`
JOIN `offering` o ON o.`session_id` = s.`session_id`
JOIN `offering_instructor` oi ON oi.`offering_id` = o.`offering_id`
WHERE oi.`instructor_group_id` = {$clean['group_id']}
AND (c.`archived` = 1 OR c.`locked` = 1)
AND c.`deleted` = 0
AND s.`deleted` = 0

UNION

SELECT DISTINCT c.`course_id`
FROM `course` c
JOIN `session` s ON s.`course_id` = c.`course_id`
JOIN `ilm_session_facet_instructor` sfi ON sfi.`ilm_session_facet_id` = s.`ilm_session_facet_id`
WHERE sfi.`instructor_group_id` = {$clean['group_id']}
AND (c.`archived` = 1 OR c.`locked` = 1)
AND c.`deleted` = 0
AND s.`deleted` = 0

UNION

SELECT DISTINCT c.`course_id`
FROM `course` c
JOIN `session` s ON s.`course_id` = c.`course_id`
JOIN `offering` o ON o.`session_id` = s.`session_id`
JOIN `offering_learner` ol ON ol.`offering_id` = o.`offering_id`
JOIN `group_default_instructor` gdi ON gdi.`group_id` = ol.`group_id`
WHERE gdi.`instructor_group_id` = {$clean['group_id']}
AND (c.`archived` = 1 OR c.`locked` = 1)
AND c.`deleted` = 0
AND s.`deleted` = 0

EOL;
        $DB = $this->dbHandle;
        $queryResults = $DB->query($sql);
        return (0 < $queryResults->num_rows() ? true : false);
    }

    /**
     * @return false if an insert doesn't produce an affected row - true otherwise
     */
    public function makeUserGroupAssociations ($userIds, $groupId, &$auditAtoms)
    {
        $DB = $this->dbHandle;

        foreach ($userIds as $uid) {
            $newRow = array();
            $newRow['instructor_group_id'] = $groupId;
            $newRow['user_id'] = $uid;

            $DB->insert('instructor_group_x_user', $newRow);

            if (($DB->affected_rows() == 0) || $this->transactionAtomFailed()) {
                return false;
            }

            array_push($auditAtoms, $this->auditEvent->wrapAtom($uid, 'user_id',
                                                                'instructor_group_x_user',
                                                                Audit_Event::$CREATE_EVENT_TYPE));
        }

        return true;
    }

    /*
     * @return an array with the key 'error' in an error case, or the keys group_id and title
     */
    public function addNewEmptyGroup ($newContainerNumber, $schoolId, &$auditAtoms)
    {
        $rhett = array();

        $title = $this->makeDefaultGroupTitleForSuffix($newContainerNumber);
        $newId = $this->makeNewRow($title, $schoolId, $auditAtoms);

        if (($newId == null) || ($newId == -1) || ($newId == 0)) {
            $lang = $this->getLangToUse();
            $msg = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);

            $rhett['error'] = $msg;
        }
        else {
            $rhett['instructor_group_id'] = $newId;
            $rhett['title'] = $title;
            $rhett['school_id'] = $schoolId;

            array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'instructor_group_id',
                                                                $this->databaseTableName,
                                                                Audit_Event::$CREATE_EVENT_TYPE, 1));
        }

        return $rhett;
    }

    /**
     * @return the number of users associated to the group with specified id; this will return -1
     *              if no group exists for the given id; this doesn't presently take into account
     *              any subgroups which this group may contain (TODO)
     */
    public function getUserCountForGroupWithId ($groupId)
    {
        $rhett = -1;

        if ($this->getRowForPrimaryKeyId($groupId) != null) {
            $DB = $this->dbHandle;

            $DB->where('instructor_group_id', $groupId);

            $queryResults = $DB->get('instructor_group_x_user');

            $rhett = $queryResults->num_rows();
        }

        return $rhett;
    }

    /**
     * @return an array of user objects (just the sql row returns) associated to the group with
     *              specified id; this will return null if no group exists for the given id.
     */
    public function getUsersForGroupWithId ($groupId)
    {
        $rhett = null;

        if ($this->getRowForPrimaryKeyId($groupId) != null) {
            $DB = $this->dbHandle;
            $DB->select('user.*');
            $DB->from('instructor_group_x_user');
            $DB->join("user", "instructor_group_x_user.user_id = user.user_id");
            $DB->where('instructor_group_x_user.instructor_group_id', $groupId);
            $DB->order_by("user.last_name, user.first_name, user.middle_name");
            $queryResults = $DB->get();
            $rhett = $queryResults->result_array();
        }

        return $rhett;
    }

    /*
     * @return an array whose each row is an instructor group db row with its associated members
     */
    public function getModelArrayForSchoolId ($schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('school_id', $schoolId);
        $DB->order_by('title');
        $queryResults = $DB->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $this->getModelArrayForGroupRow($row));
        }

        return $rhett;
    }

    /**
     * Retrieves a list of instructor groups associated with a given school,
     * and optionally further filtered by a given group title(-fragment).
     * @param int $schoolId
     * @param string $search the title/title-fragment.
     * @return array a nested array of assoc. arrays, each sub-array representing an instructor group
     */
    public function getList ($schoolId, $search = '')
    {
        $rhett = array();
        $DB = $this->dbHandle;
        if ('' !== trim($search)) {
            $len = strlen($search);
            if (Abstract_Ilios_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
                $DB->like('title', $search);
            } else {
                $DB->like('title', $search, 'after');
            }
        };
        $DB->where('school_id', $schoolId);
        $DB->order_by('title');
        $queryResults = $DB->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
        	$rhett[] = $row;
        }

        return $rhett;
    }

    /**
     * Deletes a given instructor group and all its offering and user associations with it.
     * @param int $groupId
     * @param array $autitAtoms
     * @return boolean TRUE on success, FALSE if the deletion failed
     */
    public function deleteGroupWithInstructorGroupId ($groupId, &$auditAtoms)
    {
        $rhett = false;

        // delete user/group association
        $rhett = $this->_deleteUserAssociationsToGroup($groupId, $auditAtoms);

        if (! $rhett) {
            return false;
        }

        // delete offering/group association
        $rhett = $this->_deleteOfferingAssociationsToGroup($groupId, $auditAtoms);
        if (! $rhett) {
            return false;
        }

        // finally, delete the group itself
        $DB = $this->dbHandle;
        $DB->where('instructor_group_id', $groupId);
        $DB->delete($this->databaseTableName);

        if ($this->transactionAtomFailed()) {
            return false;
        }
        if (0 < $this->db->affected_rows()) {
            $auditAtoms[] = $this->auditEvent->wrapAtom($groupId, 'instructor_group_id',
                $this->databaseTableName, Audit_Event::$DELETE_EVENT_TYPE, 1);
        }
        return true;
    }

    /**
     * @return an error string or null if the save was apparently successful (the check is not
     *              robust (which would be requerying the db to make sure that no row exists for
     *              the deleted id (TODO))
     */
    public function saveGroup ($groupId, $schoolId, $title, $users, &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $DB->where('school_id', $schoolId);
        $DB->where('title', $title);
        $DB->where('instructor_group_id !=', $groupId);
        $queryResults = $DB->get($this->databaseTableName);
        if ($queryResults->num_rows() > 0) {
            $lang = $this->getLangToUse();
            $msg = $this->i18nVendor->getI18NString('instructor_groups.error.preexisting_title',
                                                    $lang);

            return $msg . " '" . $title . "'";
        }

        $updatedRow = array();

        $updatedRow['title'] = $title;

        $DB->where('instructor_group_id', $groupId);
        $DB->update($this->databaseTableName, $updatedRow);

        if ($this->transactionAtomFailed()) {
            return "There was a Database Deadlock error.";
        }

        array_push($auditAtoms, $this->auditEvent->wrapAtom($groupId, 'instructor_group_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE, 1));

        if (! $this->_deleteUserAssociationsToGroup($groupId, $auditAtoms)) {
            return "There was a Database Deadlock error.";
        }

        if (! $this->makeUserGroupAssociations($users, $groupId, $auditAtoms)) {
            $lang = $this->getLangToUse();
            $msg = $this->i18nVendor->getI18NString('instructor_groups.error.failed_associations',
                                                    $lang);

            return $msg;
        }

        return null;
    }

    /**
     * @todo add code docs
     */
    protected function makeNewRow ($title, $schoolId, &$auditAtoms)
    {
        $newRow = array();
        $newRow['instructor_group_id'] = null;

        $newRow['title'] = $title;
        $newRow['school_id'] = $schoolId;

        $DB = $this->dbHandle;
        $DB->insert($this->databaseTableName, $newRow);

        return $DB->insert_id();
    }

    /**
     * @todo add code docs
     */
    protected function makeDefaultGroupTitleForSuffix ($groupNameSuffix)
    {
        $lang = $this->getLangToUse();
        $groupNamePrefix = $this->i18nVendor->getI18NString('instructor_groups.name_prefix', $lang);

        return $groupNamePrefix . ' ' . $groupNameSuffix;
    }

    /**
     * @todo add code docs
     */
    protected function getModelArrayForGroupRow ($row)
    {
        $rhett = array();

        $rhett['instructor_group_id'] = $row['instructor_group_id'];
        $rhett['title'] = $row['title'];
        $rhett['school_id'] = $row['school_id'];

        $rhett['users'] = $this->getUsersForGroupWithId($rhett['instructor_group_id']);

        return $rhett;
    }

    /**
     * Deletes any user associations to a given group.
     * @param int $groupId the group id
     * @param array $auditAtoms audit trial
     * @return boolean TRUE on success, FALSE on failure
     */
    protected function _deleteUserAssociationsToGroup ($groupId, &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $DB->where('instructor_group_id', $groupId);
        $DB->delete('instructor_group_x_user');

        if ($this->transactionAtomFailed()) {
            return false;
        }

        if (0 < $this->db->affected_rows()) {
            $auditAtoms[] = $this->auditEvent->wrapAtom($groupId, 'instructor_group_id',
                'instructor_group_x_user', Audit_Event::$DELETE_EVENT_TYPE);
        }
        return true;
    }

    /**
     * Deletes any offering associations to a given group.
     * @param int $groupId the group id
     * @param array $auditAtoms audit trial
     * @return boolean TRUE on success, FALSE on failure
     */
    protected function _deleteOfferingAssociationsToGroup ($groupId,  &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $DB->where('instructor_group_id', $groupId);
        $DB->delete('offering_instructor');

        if ($this->transactionAtomFailed()) {
            return false;
        }

        if (0 < $this->db->affected_rows()) {
            $auditAtoms[] = $this->auditEvent->wrapAtom($groupId, 'instructor_group_id',
                'offering_instructor', Audit_Event::$DELETE_EVENT_TYPE);
        }
        return true;
    }
}
