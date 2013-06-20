<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "group" table.
 */
class Group extends Ilios_Base_Model
{

    public function __construct ()
    {
        parent::__construct('group', array('group_id'));

        $this->load->model('Canned_Queries', 'queries', TRUE);
        $this->load->model('User', 'user', TRUE);
        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
    }

    /**
     * TODO this is a potentially very expensive operation
     */
    public function getUserGroupTreeFilteredOnUserNameAndCohort ($name, $cohortId, $rootGroupId)
    {
        $rhett = array();

        $queryResults = $this->user->getUsersWithCohortIdFilteredOnName($name, $cohortId, true);
        foreach ($queryResults->result_array() as $row) {
            $model = array();

            foreach ($row as $key => $value) {
                $model[$key] = $value;
            }

            $queryString = 'SELECT `group`.`group_id` AS `group_id` '
                                . 'FROM `group`, `group_x_user` '
                                . 'WHERE `group_x_user`.`user_id` = "' . $row['user_id'] . '" '
                                .           'AND `group_x_user`.`group_id` = `group`.`group_id` '
                                .           'AND `group`.`parent_group_id` IS NOT NULL';

            $guResults = $this->db->query($queryString);
            if ($guResults->num_rows() == 0) {
                $model['parent_chain'] = $this->getParentChain($rootGroupId);
            }
            else {
                $model['parent_chain'] = $this->getParentChain($guResults->first_row()->group_id);
            }

            array_push($rhett, $model);
        }

        return $rhett;
    }

    /**
     * Creates a learner sub-group to a given learner-group from
     * and enrolls unassigned members of a given cohort in it.
     *
     * NOTE: The transactionality of this functionality is best handled by the caller of this method as
     *  opposed to within the method itself.
     *
     * @param int $userCount the desired enrollment size of the subgroup; if there are not enough users
     *    to meet this, a subgroup with a smaller population will be constructed
     * @param int $cohortId the cohort id
     * @param int $masterGroupId the parent group id
     * @param string $groupNameSuffix the name of suffix of the sub-group to create
     * @param array $auditAtoms the audit trail
     * @return array an associative array of three elements keyed off by:
     *     'group_title' ... name of the created subgroup
     *     'group_id' ... the new group id
     *     'enrollment' ... the actual number of users that were enrolled in the new subgroup
     */
    public function makeSubgroupOfUnassignedUsersFromCohortId ($userCount, $cohortId, $masterGroupId, $groupNameSuffix, &$auditAtoms)
    {
        $rhett = array();

        $clean = array();
        $clean['user_count'] = (int) $userCount;
        $clean['cohort_id'] = (int) $cohortId;
        $clean['master_group_id'] = (int) $masterGroupId;

        $queryString = "CALL user_ids_from_cohort_and_master_group({$clean['user_count']}, {$clean['cohort_id']}, {$clean['master_group_id']})";

        $rhett['group_title'] = $this->makeDefaultGroupTitleForSuffix($masterGroupId, $groupNameSuffix);

        $rhett['group_id'] = $this->makeNewRow($rhett['group_title'], $masterGroupId, $auditAtoms);

        $queryResults = $this->db->query($queryString);
        $rhett['enrollment'] = $queryResults->num_rows();

        // due to trying to perform inserts midst loop of the stored proc return, receiving:
        //      Commands out of sync; you can't run this command now
        //  i'm iterating through the results completely first, then iterating through the
        //  array of uids
        $usableUIDs = array();
        foreach ($queryResults->result_array() as $row) {
            $usableUIDs[] = $row['uid'];
        }

        $this->reallyFreeQueryResults($queryResults);

        $this->makeUserGroupAssociations($usableUIDs, $rhett['group_id'], $auditAtoms);

        return $rhett;
    }

    /**
     * Associates a given group with a given list of users, and, optionally,
     * disassociates these users from another given group.
     *
     * @param array $userIds a list of user ids
     * @param int $groupId the id of the group to associate users with
     * @param array $auditAtoms the audit trail
     * @param int $deleteFromId if non-null, the user ids are de-associated from this group id
     * @return boolean FALSE if an insert doesn't produce an affected row - TRUE otherwise
     */
    public function makeUserGroupAssociations ($userIds, $groupId, &$auditAtoms, $deleteFromId = null)
    {
        foreach ($userIds as $uid) {
            $newRow = array();
            $newRow['group_id'] = $groupId;
            $newRow['user_id'] = $uid;

            $this->db->insert('group_x_user', $newRow);

            if ($this->transactionAtomFailed() || ($this->db->affected_rows() == 0)) {
                return false;
            }

            $auditAtoms[] = $this->auditEvent->wrapAtom($uid, 'user_id', 'group_x_user', Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);

            if ($deleteFromId != null) {
                $this->db->where('group_id', $deleteFromId);
                $this->db->where('user_id', $uid);

                $this->db->delete('group_x_user');

                if ($this->transactionAtomFailed()) {
                    return false;
                }

                $auditAtoms[] = $this->auditEvent->wrapAtom($deleteFromId, 'group_id', 'group_x_user', Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
            }
        }

        return true;
    }

    /*
     * @param $parentGroupId if this is equal to -1, then the group created will be a master group
     *                          of the cohort and be populated with all of the users in the cohort
     * @return an array with the key 'error' in an error case, or the keys group_id and title
     */
    public function addNewGroup ($cohortId, $parentGroupId, $newContainerNumber, &$auditAtoms)
    {
        $rhett = array();

        $title = $this->makeDefaultGroupTitleForSuffix($parentGroupId, $newContainerNumber);
        $newId = $this->makeNewRow($title, $parentGroupId, $auditAtoms);

        if (($newId == null) || ($newId == -1) || ($newId == 0)) {
            $lang = $this->getLangToUse();
            $msg = $this->languagemap->getI18NString('general.error.db_insert', $lang);

            $rhett['error'] = $msg;
        }
        else {
            if ($parentGroupId == -1) {
                $newRow = array();
                $newRow['cohort_id'] = $cohortId;
                $newRow['group_id'] = $newId;

                $this->db->insert('cohort_master_group', $newRow);
                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($newId, 'group_id', 'cohort_master_group',
                                                       Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));

                $queryResults = $this->user->getUsersForCohort($cohortId);
                foreach ($queryResults->result_array() as $row) {
                    $newRow = array();
                    $newRow['user_id'] = $row['user_id'];
                    $newRow['group_id'] = $newId;

                    $this->db->insert('group_x_user', $newRow);
                    array_push($auditAtoms,
                               $this->auditEvent->wrapAtom($newId, 'group_id', 'group_x_user',
                                                           Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));
                }
            }

            $rhett['group_id'] = $newId;
            $rhett['title'] = $title;
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

            $this->db->where('group_id', $groupId);

            $queryResults = $this->db->get('group_x_user');

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
            $this->db->select('user.*');
            $this->db->where('group_x_user.group_id', $groupId);
            $this->db->join('user', 'group_x_user.user_id = user.user_id');
            $this->db->order_by('user.last_name', 'ASC');
            $this->db->order_by('user.first_name', 'ASC');
            $this->db->order_by('user.middle_name', 'ASC');
            $this->db->order_by('user.user_id', 'ASC');
            $queryResults = $this->db->get('group_x_user');

            $rhett = array();

            foreach ($queryResults->result_object() as $row) {
                $rhett[] = $row;
            }
        }

        return $rhett;
    }

    /**
     * Deletes all user/group associations for a given list of groups.
     * @param array groupIdArray 1-N group ids.
     * @param array $auditAtoms The audit trail.
     * @return boolean TRUE on successful deletion, FALSE on transaction failure
     *  or if there were no associations to be deleted.
     *
     * @todo Ambiguous information is conveyed in a FALSE return value. Fix this [ST 2013/06/20].
     */
    public function deleteUserGroupAssociationForGroupIds ($groupIdArray, &$auditAtoms)
    {
        $len = count($groupIdArray);

        for ($i = 0; $i < $len; $i++) {
            if ($i == 0) {
                $this->db->where('group_id', $groupIdArray[$i]);
            } else {
                $this->db->or_where('group_id', $groupIdArray[$i]);
            }

            $auditAtoms[] = $this->auditEvent->wrapAtom($groupIdArray[$i], 'group_id', 'group_x_user',
                Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
        }

        $this->db->delete('group_x_user');

        $rhett = $this->transactionAtomFailed();

        if (! $rhett) {
            $rhett = ($this->db->affected_rows() == 0) ? false : true;
        }

        return $rhett;
    }

    /*
     * @return an array of group model arrays; a group model associative array has keys:
     *              . group_id
     *              . title
     *              . parent_group_id
     *              . location
     *              . instructors
     *              . users (which has a value of an associative array of User SQL result rows)
     *              . subgroups     (associated to an array recursive-like of this-call-returns)
     *          or null if there is no group with that id (as opposed to an empty array if there
     *          is a group but it has no subgroups).
     */
    public function getSubgroupsForGroupId ($groupId)
    {
        $rhett = null;

        if ($this->getRowForPrimaryKeyId($groupId) != null) {
            $rhett = array();

            $this->db->where('parent_group_id', $groupId);
            $this->db->order_by('group_id', 'asc');
            $queryResults = $this->db->get($this->databaseTableName);

            foreach ($queryResults->result_array() as $row) {
                $modelArray = $this->getModelArrayForGroupRow($row);

                $gid = $modelArray['group_id'];

                $modelArray['subgroups'] = $this->getSubgroupsForGroupId($gid);
                $modelArray['courses'] = $this->queries->getCourseIdAndTitleForLearnerGroup($gid);

                array_push($rhett, $modelArray);
            }
        }

        return $rhett;
    }

    public function getQueryResultsForInstructorsForGroup ($groupId)
    {
        $this->db->where('group_id', $groupId);

        return $this->db->get('group_default_instructor');
    }

    /**
     * @todo add code docs
     */
    public function getModelArrayForGroupId ($groupId)
    {
        $this->db->where('group_id', $groupId);
        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            return $this->getModelArrayForGroupRow($row);
        }

        return null;
    }

    /**
     * Deletes a given group, its sub-groups (recursively)
     * and any cohort/session/offering associations to it.
     *
     * Transactions must be handled outside this method.
     *
     * @param $groupId the group id
     * @return boolean currently always TRUE.
     * @todo update code to conditionally return TRUE based on whether an an actual deletion occurred or not.
     */
    public function deleteGroupWithGroupId ($groupId)
    {
        // descend subgroup tree recursively
        // and work our way up to capture the entire user reassign
        $this->db->where('parent_group_id', $groupId);
        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            $this->deleteGroupWithGroupId($row['group_id']);
        }

        // delete group associations
        $this->db->where('group_id', $groupId);
        $this->db->delete('group_x_user');

        $this->db->where('group_id', $groupId);
        $this->db->delete('cohort_master_group');

        $this->db->where('group_id', $groupId);
        $this->db->delete('offering_learner');

        $this->db->where('group_id', $groupId);
        $this->db->delete('ilm_session_facet_learner');

        $this->db->where('group_id', $groupId);
        $this->db->delete($this->databaseTableName);

        return true;
    }

    /**
     * Transaction are assumed to be handled outside of this method
     *
     * @return an error string or null if the save was apparently successful (the check is not
     *              robust (which would be requerying the db to make sure that no row exists for
     *              the deleted id (TODO))
     */
    public function saveGroupForGroupId ($groupId, $title, $instructors, $location,
        $parentGroupId, &$auditAtoms, $checkUniqueTitle = true)
    {
        if ($checkUniqueTitle) {
            $this->db->where('parent_group_id', $parentGroupId);
            $this->db->where('title', $title);
            $this->db->where('group_id !=', $groupId);
            $queryResults = $this->db->get($this->databaseTableName);
            if ($queryResults->num_rows() > 0) {
                $lang = $this->getLangToUse();
                $msg = $this->languagemap->getI18NString('groups.error.preexisting_title', $lang);

                return $msg . " '" . $title . "'";
            }
        }

        $updatedRow = array();

        $updatedRow['title'] = $title;
        $updatedRow['location'] = is_null($location) ? '' : $location;

        $this->db->where('group_id', $groupId);
        $this->db->update($this->databaseTableName, $updatedRow);

        $auditAtoms[] = $this->auditEvent->wrapAtom($groupId, 'group_id', $this->databaseTableName,
            Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE, 1);

        $this->deleteInstructorsForGroup($groupId, $auditAtoms);
        $this->saveInstructorsForGroup($groupId, $instructors, $auditAtoms);

        return null;
    }

    protected function saveInstructorsForGroup ($groupId, $instructors, &$auditAtoms)
    {
        foreach ($instructors as $instructorModel) {
            $newRow = array();
            $newRow['group_id'] = $groupId;

            $columnName = 'user_id';
            if ($instructorModel['isGroup'] == 1) {
                $columnName = 'instructor_group_id';
            }
            $newRow[$columnName] = $instructorModel['dbId'];

            $this->db->insert('group_default_instructor', $newRow);

            array_push($auditAtoms, $this->auditEvent->wrapAtom($groupId, 'group_id',
                                                                'group_default_instructor',
                                                                Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));
        }
    }

    /**
     * @return given a hypothetical chain of topMostParent -> A -> B -> startingGroupId, this
     *              method would return an array of four elements {{startingGroupId,id}, {B,id},
     *                                                             {A,id}, {topMostParent,id}}
     */
    protected function getParentChain ($startingGroupId)
    {
        $rhett = array();

        $row = null;

        $groupId = $startingGroupId;

        do {
            $this->db->where('group_id', $groupId);

            $queryResults = $this->db->get($this->databaseTableName);
            $row = $queryResults->first_row();

            if ($row != null) {
                $model = array();

                $model['title'] = $row->title;
                $model['group_id'] = $row->group_id;

                array_push($rhett, $model);

                $groupId = $row->parent_group_id;
            }
        }
        while (($row != null) && ($groupId != null));

        return $rhett;
    }

    protected function makeNewRow ($title, $parentGroupId, &$auditAtoms) {
        $newRow = array();
        $newRow['group_id'] = null;

        $newRow['title'] = $title;
        $newRow['parent_group_id'] = (($parentGroupId < 1) ? null : $parentGroupId);

        $this->db->insert($this->databaseTableName, $newRow);

        $newId = $this->db->insert_id();
        array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'group_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));

        return $newId;
    }

    protected function makeDefaultGroupTitleForSuffix ($parentGroupId, $groupNameSuffix) {
        $parentGroupName = null;

        if ($parentGroupId != -1) {
            $row = $this->getRowForPrimaryKeyId($parentGroupId);

            if ($row != null) {
                $parentGroupName = $row->title;
            }
        }

        if ($parentGroupName == null) {
            $lang = $this->getLangToUse();
            $groupNamePrefix = $this->languagemap->getI18NString('groups.name_prefix', $lang);
        }
        else {
            $groupNamePrefix = $parentGroupName;
        }

        return $groupNamePrefix . ' ' . $groupNameSuffix;
    }

    protected function getModelArrayForGroupRow ($row)
    {
        $rhett = array();

        $rhett['group_id'] = $row['group_id'];
        $rhett['title'] = $row['title'];
        $rhett['parent_group_id'] = $row['parent_group_id'];
        $rhett['location'] = ($row['location'] != null ? $row['location'] : '');

        $rhett['instructors'] = $this->getInstructorsForGroup($rhett['group_id']);

        $rhett['users'] = $this->getUsersForGroupWithId($rhett['group_id']);

        return $rhett;
    }

    protected function getInstructorsForGroup ($groupId)
    {
        $rhett = array();

        $queryResults = $this->getQueryResultsForInstructorsForGroup($groupId);

        foreach ($queryResults->result_array() as $row) {
            if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                $igRow = $this->instructorGroup->getRowForPrimaryKeyId($row['instructor_group_id']);
                if ($igRow) {
                    array_push($rhett, $this->convertStdObjToArray($igRow));
                }
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($rhett, $this->convertStdObjToArray($userRow));
            }
        }

        return $rhett;
    }

    /**
     * Transactions must be handled outside this method
     */
    protected function deleteInstructorsForGroup ($groupId, &$auditAtoms)
    {
        $this->db->where('group_id', $groupId);
        $this->db->delete('group_default_instructor');

        array_push($auditAtoms, $this->auditEvent->wrapAtom($groupId, 'group_id',
                                                            'group_default_instructor',
                                                            Ilios_Model_AuditUtils::DELETE_EVENT_TYPE));
    }
}

