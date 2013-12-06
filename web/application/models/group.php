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

        $rhett['group_id'] = $this->makeNewRow($rhett['group_title'], $masterGroupId, $cohortId, $auditAtoms);

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

        // add user/group associations
        $this->_associateWithJoinTable('group_x_user', 'group_id', $rhett['group_id'], 'user_id', $usableUIDs, $auditAtoms);

        return $rhett;
    }

    /**
     * Updates the user/group associations for a given list of users and a given group.
     * This may entail adding and removing associations.
     * @param int $groupId The group id.
     * @param array $users An array of arrays. Each item is an associative array, representing a user.
     * @param array $existingUserIds An array of user-ids, each of which is already associated with the given group
     *      as learner..
     * @param array $auditAtoms The audit trail.
     * @see Ilios_Base_Model::_saveJoinTableAssociations()
     */
    public function updateUserToGroupAssociations ($groupId, array $users, array $existingUserIds, array &$auditAtoms)
    {
        $this->_saveJoinTableAssociations('group_x_user', 'group_id', $groupId, 'user_id', $users, $existingUserIds,
            'user_id', $auditAtoms);
    }

    /**
     * Updates the instructor/group associations for a given list of users and a given group.
     * This may entail adding and removing associations.
     * @param int $groupId The group id.
     * @param array $users An array of arrays. Each item is an associative array, representing a user.
     * @param array $existingUserIds An array of user-ids, each of which is already associated with the given group as
     *      instructor.
     * @param array $auditAtoms The audit trail.
     * @see Ilios_Base_Model::_saveJoinTableAssociations()
     */
    public function updateInstructorToGroupAssociations ($groupId, array $users, array $existingUserIds,
                                                         array &$auditAtoms)
    {
        $this->_saveJoinTableAssociations('group_x_instructor', 'group_id', $groupId, 'user_id', $users,
            $existingUserIds, 'dbId', $auditAtoms);
    }

    /**
     * Updates the instructor-group/learner-group associations for a given list of instructors-groups and a given
     * learner-group.
     * This may entail adding and removing associations
     * @param int $groupId The group id.
     * @param array $instructorGroups An array of arrays. Each item is an associative array, representing an
     *      instructor-group.
     * @param array $existingInstructorGroupIds An array of instructor-group-ids, each of which is already associated
     *      with the given learner-group.
     * @param array $auditAtoms The audit trail.
     * @see Ilios_Base_Model::_saveJoinTableAssociations()
     */
    public function updateInstructorGroupToGroupAssociations ($groupId, array $instructorGroups,
                                                              array $existingInstructorGroupIds, array &$auditAtoms)
    {
        $this->_saveJoinTableAssociations('group_x_instructor_group', 'group_id', $groupId, 'instructor_group_id',
            $instructorGroups, $existingInstructorGroupIds, 'dbId', $auditAtoms);
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
        $newId = $this->makeNewRow($title, $parentGroupId, $cohortId, $auditAtoms);

        if (($newId == null) || ($newId == -1) || ($newId == 0)) {
            $msg = $this->languagemap->getI18NString('general.error.db_insert');

            $rhett['error'] = $msg;
        }
        else {
            if ($parentGroupId == -1) {
                $newRow = array();
                $newRow['cohort_id'] = $cohortId;
                $newRow['group_id'] = $newId;

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
     * Retrieves a list of record ids for users that are associated as learners with a given group.
     * @param int $groupId The group id.
     * @return array An array of user ids.
     */
    public function getIdsForUsersInGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<< EOL
SELECT `user_id`
FROM `group_x_user`
WHERE `group_id` = {$clean['group_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row['user_id'];
            }
        }
        $query->free_result();
        return $rhett;
    }
    /**
     * Retrieves a list of record ids for users that are associated as instructors with a given group.
     * @param int $groupId The group id.
     * @return array An array of user ids.
     */
    public function getIdsForInstructorsInGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<< EOL
SELECT `user_id`
FROM `group_x_instructor`
WHERE `group_id` = {$clean['group_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row['user_id'];
            }
        }
        $query->free_result();
        return $rhett;
    }
    /**
     * Retrieves a list of record ids for instructor-groups that are associated with a given learner-group.
     * @param int $groupId The group id.
     * @return array An array of instructor-group ids.
     */
    public function getIdsForInstructorGroupsInGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<< EOL
SELECT `instructor_group_id`
FROM `group_x_instructor_group`
WHERE `group_id` = {$clean['group_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row['instructor_group_id'];
            }
        }
        $query->free_result();
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

    /**
     * Retrieves a query result set containing the identifiers of all instructors and instructor-groups
     * associated with a given group.
     * See JOIN table <code>group_x_instructor</code> and <code>group_x_instructor_group</code>.
     * @param int $groupId The group id.
     * @return CI_DB_result The query result object.
     */
    public function getQueryResultsForInstructorsForGroup ($groupId)
    {
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<<EOL
SELECT group_id, user_id, NULL AS instructor_group_id
FROM group_x_instructor
WHERE group_id = ${clean['group_id']}
UNION
SELECT group_id, NULL as user_id, instructor_group_id
FROM group_x_instructor_group
WHERE group_id = ${clean['group_id']}
EOL;
        return $this->db->query($sql);
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
        $this->db->delete('offering_x_group');

        $this->db->where('group_id', $groupId);
        // $this->db->delete('ilm_session_facet_x_group');

        $this->db->where('group_id', $groupId);
        $this->db->delete($this->databaseTableName);

        return true;
    }

    /**
     * Updates the properties of a given group.
     * @param int $groupId The group id.
     * @param string $title The group's title.
     * @param string $location The group's default location.
     * @param int|null $parentGroupId The parent group id. NULL if the given group is a root group.
     * @param array $auditAtoms The audit trail
     * @param boolean $checkUniqueTitle Set to TRUE to ensure uniqueness of group title amongst siblings.
     * @return null|string NULL on success, or an error message on failure.
     */
    public function saveGroupForGroupId ($groupId, $title, $location, $parentGroupId, &$auditAtoms,
                                         $checkUniqueTitle = true)
    {
        if ($checkUniqueTitle) {
            $this->db->where('parent_group_id', $parentGroupId);
            $this->db->where('title', $title);
            $this->db->where('group_id !=', $groupId);
            $queryResults = $this->db->get($this->databaseTableName);
            if ($queryResults->num_rows() > 0) {
                $msg = $this->languagemap->getI18NString('groups.error.preexisting_title');

                return $msg . " '" . $title . "'";
            }
        }


        $updatedRow = array();

        $updatedRow['title'] = $title;
        $updatedRow['location'] = is_null($location) ? '' : $location;

        $this->db->where('group_id', $groupId);
        $this->db->update($this->databaseTableName, $updatedRow);

        if (0 < $this->db->affected_rows()) {
            $auditAtoms[] = $this->auditEvent->wrapAtom($groupId, 'group_id', $this->databaseTableName,
                Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE, 1);
        }
        return null;
    }

    /**
     * Associates a given list of instructors/instructor-groups with a given group.
     * @param int $groupId The group id.
     * @param array $instructors An array of nested arrays. Each item represents either an instructor or an
     *      instructor-group.
     * @param array $auditAtoms The audit trail
     */
    public function saveInstructorsForGroup ($groupId, $instructors, &$auditAtoms)
    {
        $userIds = array();
        $instructorGroupIds = array();
        foreach ($instructors as $instructorModel) {
            if (1 == $instructorModel['isGroup']) { // separate user ids from instructor group ids
                $instructorGroupIds[] = $instructorModel['dbId'];
            } else {
                $userIds[] = $instructorModel['dbId'];
            }
        }
        // add group/instructor associations
        $this->_associateWithJoinTable('group_x_instructor', 'group_id', $groupId, 'user_id', $userIds, $auditAtoms);
        // add group/instructor_group associations
        $this->_associateWithJoinTable('group_x_instructor_group', 'group_id', $groupId, 'instructor_group_id',
            $instructorGroupIds, $auditAtoms);
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

    protected function makeNewRow ($title, $parentGroupId, $cohortId, &$auditAtoms) {
        $newRow = array();
        $newRow['group_id'] = null;

        $newRow['title'] = $title;
        $newRow['parent_group_id'] = (($parentGroupId < 1) ? null : $parentGroupId);
        $newRow['cohort_id'] = $cohortId;

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
            $groupNamePrefix = $this->languagemap->getI18NString('groups.name_prefix');
        }
        else {
            $groupNamePrefix = $parentGroupName;
        }

        return $groupNamePrefix . ' ' . $groupNameSuffix;
    }

    /**
     * Populates a given group record with associated entities, such as instructors, instructor-groups and learners.
     * @param array $row The raw group record as associative array.
     * @return array The fully populated group record as associative array, with the following properties:
     *    'group_id' ... The group id.
     *    'title' ... The group's title.
     *    'parent_group_id' ... The parent group id (NULL if this group is a root group).
     *    'location' ... The group's location.
     *    'instructors' ... A list of instructors and instructor-groups associated with this group. Each item is an
     *          associative array, containing either a user record or an instructor-group record.
     *    'users' ... A list of learners associated with this group. Each item is an associative array, containing a
     *          user record.
     */
    protected function getModelArrayForGroupRow ($row)
    {
        $rhett = array();

        $rhett['group_id'] = $row['group_id'];
        $rhett['title'] = $row['title'];
        $rhett['parent_group_id'] = $row['parent_group_id'];
        $rhett['location'] = ($row['location'] != null ? $row['location'] : '');

        $rhett['instructors'] = $this->getInstructorsForGroup($rhett['group_id']);
        $rhett['instructors'] = array_merge($rhett['instructors'],
            $this->getInstructorGroupsForGroup($rhett['group_id']));

        $rhett['users'] = $this->getUsersForGroupWithId($rhett['group_id']);

        return $rhett;
    }

    /**
     * Retrieves a list of users that are associated as instructors with a given-learner group.
     * @param int $groupId The group id.
     * @return array An array of associative arrays. Each item represents a user.
     */
    protected function getInstructorsForGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<<EOL
SELECT u.*
FROM `user` u
JOIN `group_x_instructor` gdi ON gdi.`user_id` = u.`user_id`
WHERE gdi.`group_id` = {$clean['group_id']}
EOL;
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
     * Retrieves a list of instructor-groups that are associated with a given learner-group.
     * @param int $groupId The group id.
     * @return array An array of associative arrays. Each item represents an instructor group.
     */
    protected function getInstructorGroupsForGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;
        $sql =<<<EOL
SELECT ig.*
FROM `instructor_group` ig
JOIN `group_x_instructor_group` gdi ON gdi.`instructor_group_id` = ig.`instructor_group_id`
WHERE gdi.`group_id` = {$clean['group_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }
}
