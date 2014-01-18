<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the cohort table.
 */
class Cohort extends Ilios_Base_Model
{

    public function __construct ()
    {
        parent::__construct('cohort', array('cohort_id'));

        $this->load->model('Group', 'group', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /*
     * @return an array with keys 'program_title', 'cohort_title', 'start_year', 'cohort_id', 'school_id'
     */
    public function getProgramCohortDetailsForCohortId ($cohortId)
    {
        $rhett = array();
        $clean = array();
        $clean['cohort_id'] = (int) $cohortId;
        $queryString =<<<EOL
SELECT
`cohort`.`cohort_id` AS `cohort_id`,
`program`.`owning_school_id` AS `owning_school_id`,
`program`.`title` AS `program_title`,
`program_year`.`start_year` AS `start_year`,
`cohort`.`title` AS `cohort_title`,
`program_year`.`program_year_id` AS `program_year_id`
FROM `program`, `program_year`, `cohort`
WHERE `cohort`.`cohort_id` = {$clean['cohort_id']}
AND `cohort`.`program_year_id` = `program_year`.`program_year_id`
AND `program`.`program_id` = `program_year`.`program_id`
EOL;
        $queryResults = $this->db->query($queryString);
        // there will only ever be at most 1 row
        foreach ($queryResults->result_array() as $row) {
            $rhett['program_title'] = $row['program_title'];
            $rhett['program_year_id'] = $row['program_year_id'];
            $rhett['cohort_title'] = $row['cohort_title'];
            $rhett['start_year'] = $row['start_year'];
            $rhett['cohort_id'] = $row['cohort_id'];
            $rhett['school_id'] = $row['owning_school_id'];
        }

        return $rhett;
    }

    /**
     * @return null if there is no cohort with the given program year id, otherwise the db row
     */
    public function getCohortWithProgramYearId ($programYearId)
    {
        $rhett = null;

        $this->db->where('program_year_id', $programYearId);

        $queryResults = $this->db->get($this->databaseTableName);

        if ($queryResults->num_rows() > 0) {
            $rhett = $queryResults->first_row();
        }

        return $rhett;
    }

    /**
     * Transactions are assumed to be handled outside this block
     *
     * @return false if there is no cohort with the given program year id, otherwise true
     */
    public function deleteCohortAndAssociationsForProgramYear ($programYearId, &$auditAtoms)
    {
        $this->db->select('cohort_id');
        $this->db->where('program_year_id', $programYearId);
        $queryResults = $this->db->get($this->databaseTableName);

        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();

            return $this->_deleteCohort($row->cohort_id, $auditAtoms);
        }

        return false;
    }

    /**
     * @return an empty array if there is no cohort with the given program year id, otherwise all of
     *                  the master / top-level group ids associated to the cohort
     */
    public function getGroupIdsForCohortWithProgramYear ($programYearId)
    {
        $rhett = array();

        $this->db->select('cohort_id');

        $this->db->where('program_year_id', $programYearId);

        $queryResults = $this->db->get($this->databaseTableName);

        $cohortId = -1;
        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();

            $cohortId = $row->cohort_id;
        }

        if ($cohortId != -1) {
            $rhett = $this->getGroupIdsForCohortWithId($cohortId);
        }

        return $rhett;
    }

    /**
     * @return all of the master / top-level group ids associated to the cohort
     */
    public function getGroupIdsForCohortWithId ($cohortId)
    {
        $rhett = array();

        $this->db->where('group.cohort_id', $cohortId);
        $this->db->where('group.parent_group_id IS NULL');
        $this->db->order_by('group.title');

        $queryResults = $this->db->get('group');

        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row['group_id']);
        }

        return $rhett;
    }

    /**
     * @return the number of users associated to the cohort with specified id; this will return -1
     *              if no cohort exists for the given id
     */
    public function getUserCountForCohort ($cohortId)
    {
        $rhett = -1;

        if ($this->getRowForPrimaryKeyId($cohortId) != null) {
            $queryResults = $this->user->getUsersForCohort($cohortId);
            $rhett = $queryResults->num_rows();
        }

        return $rhett;
    }

    /**
     * Retrieves the "full title" (program title + cohort title) of a given cohort.
     * @param int $cohortId the cohort id
     * @return string the program/cohort title, or a blank string if not found
     */
    public function getFullCohortTitle ($cohortId)
    {
        $clean = array();
        $clean['cohort_id'] = (int) $cohortId;

        $queryString =<<< EOL
SELECT `cohort`.`title` AS `cohort_title`,
`program`.`title` AS `program_title`
FROM `program_year`
JOIN `program` ON `program`.`program_id` = `program_year`.`program_id`
JOIN `cohort` ON `cohort`.`program_year_id` = `program_year`.`program_year_id`
WHERE `cohort`.`cohort_id` = {$clean['cohort_id']}
EOL;

        $queryResults = $this->db->query($queryString);
        if ($queryResults->num_rows() == 0) {
            return '';
        }

        $row = $queryResults->first_row();

        return $row->program_title . ' - ' . $row->cohort_title;
    }

    /**
     * Retrieves the cohort programs for a given school and user.
     * @param int $schoolId
     * @param int $userId
     * @return an associative array containing a key 'map' valued to an associative array; the
     *                  results.map is an associative array constructed on the server side featuring
     *                  a key of the program title associated to a value which is an array. that
     *                  array will be a non-associative array of cohort row data, ordered
     *                  natural-ascending on cohort.title
     *          todo - no need for extra level of association in the array if we return no more than map
     */
    public function getCohortProgramTreeContent ($schoolId, $userId)
    {
        $rhett = array();
        $map = array();

        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['user_id'] = (int) $userId;

        $queryString =<<< EOL
SELECT DISTINCT d.program_title, d.program_short_title,
d.program_duration, d.owning_school_id, d.cohort_id,
d.cohort_title, d.start_year, d.program_year_id
FROM (
    SELECT DISTINCT p.`title` AS `program_title`,
    p.`short_title` AS `program_short_title`,
    p.`duration` AS `program_duration`,
    p.`owning_school_id` AS `owning_school_id`,
    c.`cohort_id` AS `cohort_id`,
    c.`title` AS `cohort_title`,
    py.`start_year` AS `start_year`,
    py.`program_year_id` AS `program_year_id`
    FROM `program` p
    JOIN `program_year` py ON py.`program_id` = p.`program_id`
    JOIN `cohort` c ON c.`program_year_id` = py.`program_year_id`
    JOIN `program_year_steward` pys ON pys.`program_year_id` = py.`program_year_id`
    WHERE
    py.`deleted` = 0
    AND py.`locked` = 0
    AND pys.`school_id` = {$clean['school_id']}

    UNION

    SELECT DISTINCT p.`title` AS `program_title`,
    p.`short_title` AS `program_short_title`,
    p.`duration` AS `program_duration`,
    p.`owning_school_id` AS `owning_school_id`,
    c.`cohort_id` AS `cohort_id`,
    c.`title` AS `cohort_title`,
    py.`start_year` AS `start_year`,
    py.`program_year_id` AS `program_year_id`
    FROM `program` p
    JOIN `program_year` py ON py.`program_id` = p.`program_id`
    JOIN `cohort` c ON c.`program_year_id` = py.`program_year_id`
    WHERE
    py.`deleted` = 0
    AND py.`locked` = 0
    AND p.`owning_school_id` = {$clean['school_id']}

    UNION

    SELECT DISTINCT p.`title` AS `program_title`,
    p.`short_title` AS `program_short_title`,
    p.`duration` AS `program_duration`,
    p.`owning_school_id` AS `owning_school_id`,
    c.`cohort_id` AS `cohort_id`,
    c.`title` AS `cohort_title`,
    py.`start_year` AS `start_year`,
    py.`program_year_id` AS `program_year_id`
    FROM `program` p
    JOIN `program_year` py ON py.`program_id` = p.`program_id`
    JOIN `cohort` c ON c.`program_year_id` = py.`program_year_id`
    WHERE
    py.`deleted` = 0
    AND py.`locked` = 0
    AND p.`program_id` IN (
        SELECT `table_row_id` FROM `permission`
        WHERE `permission`.`user_id` = {$clean['user_id']}
        AND `permission`.`table_name` = "program"
    )
) AS d
ORDER BY d.program_title, d.cohort_title
EOL;
        $queryResults = $this->db->query($queryString);

        foreach ($queryResults->result_array() as $row) {
            if (array_key_exists($row['program_title'], $map)) {
                $cohortArray = $map[$row['program_title']];
            }
            else {
                $cohortArray = array();
            }

            $cohortModel = array();
            $cohortModel['program_short_title'] = $row['program_short_title'];
            $cohortModel['program_duration'] = $row['program_duration'];
            $cohortModel['start_year'] = $row['start_year'];
            $cohortModel['cohort_id'] = $row['cohort_id'];
            $cohortModel['title'] = $row['cohort_title'];
            $cohortModel['program_year_id'] = $row['program_year_id'];
            $cohortModel['enrollment'] = $this->getUserCountForCohort($row['cohort_id']);
            $cohortModel['is_active_school'] = ((int) $row['owning_school_id'] === $clean['school_id']) ? true : false;

            $groupIds = $this->getGroupIdsForCohortWithId($row['cohort_id']);
            $groups = array();
            foreach ($groupIds as $groupId) {
                $groupRow = $this->group->getRowForPrimaryKeyId($groupId);

                $group = array();
                $group['group_id'] = $groupId;
                $group['title'] = $groupRow->title;
                $group['instructors'] = $groupRow->instructors;
                $group['location'] = $groupRow->location;

                $groups[] = $group;
            }
            $cohortModel['master_groups'] = $groups;

            $cohortArray[] = $cohortModel;

            $map[$row['program_title']] = $cohortArray;
        }

        $rhett['map'] = $map;

        return $rhett;
    }

    /**
     * Retrieves program-cohorts grouped by owning schools
     * @return array a structure of nested associative arrays
     */
    public function getProgramCohortsGroupedBySchool ()
    {
        $clean = array();

        $sql =<<<EOL
SELECT
`cohort`.`cohort_id`,
`cohort`.`title` AS `cohort_title`,
`program_year`.`program_year_id` AS `py_id`,
`program_year`.`start_year` AS `start_year`,
`program`.`title` AS `program_title`,
`school`.`school_id`,
`school`.`title` AS `school_title`
FROM `program_year`
JOIN `program` ON `program`.`program_id` = `program_year`.`program_id`
JOIN `cohort` ON `cohort`.`program_year_id` = `program_year`.`program_year_id`
JOIN `school` ON `school`.`school_id` = `program`.`owning_school_id`
WHERE `school`.`deleted` = 0
ORDER BY `program_title`, `cohort_title`, `start_year`
EOL;

        $query = $this->db->query($sql);
        if (! $query->num_rows()) {
            return array();
        }

        $rhett = array();
        foreach ($query->result_array() as $row) {
            if (! array_key_exists($row['school_id'], $rhett)) {
                $rhett[$row['school_id']] = array(
                    'school_id' => $row['school_id'],
                    'school_title' => $row['school_title'],
                    'program_cohorts' => array()
                );
            }
            $rhett[$row['school_id']]['program_cohorts'][] = array(
                'cohort_id' => $row['cohort_id'],
                'cohort_title' => $row['cohort_title'],
                'py_id' => $row['py_id'],
                'start_year' => $row['start_year'],
                'owning_school_id' => $row['school_id'],
                'program_title' => $row['program_title']
            );
        }

        $query->free_result();

        return array_values($rhett);
    }

    /**
     * Retrieves the primary cohort for a given user
     * @param int $userId
     * @return boolean|array an assoc. array representing the primary cohort, or FALSE if not found.
     */
    public function getPrimaryCohortForUser ($userId)
    {
        $clean = array();
        $clean['user_id'] = (int) $userId;

        $sql =<<<EOL
SELECT
`cohort`.`cohort_id`,
`cohort`.`title` AS `cohort_title`,
`program_year`.`program_year_id` AS `py_id`,
`program_year`.`start_year` AS `start_year`,
`program`.`title` AS `program_title`,
`program`.`owning_school_id`
FROM `program_year`
JOIN `program` ON `program`.`program_id` = `program_year`.`program_id`
JOIN `cohort` ON `cohort`.`program_year_id` = `program_year`.`program_year_id`
JOIN `user_x_cohort` ON `user_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
WHERE `user_x_cohort`.`user_id` = {$clean['user_id']}
AND `user_x_cohort`.`is_primary` = 1
EOL;

        $query = $this->db->query($sql);
        if (! $query->num_rows()) {
            return false;
        }

        $rhett = $query->first_row('array');

        $query->free_result();

        return $rhett;
    }

    /**
     * Retrieves a list of secondary (non-primary) cohorts of a given user.
     * @param int $userId the user id
     * @return array a nested array of assoc. arrays, each one representing a cohort.
     */
    public function getSecondaryCohortsForUser ($userId)
    {
        $clean = array();
        $clean['user_id'] = (int) $userId;

        $sql =<<<EOL
SELECT
`cohort`.`cohort_id`,
`cohort`.`title` AS `cohort_title`,
`program_year`.`program_year_id` AS `py_id`,
`program_year`.`start_year` AS `start_year`,
`program`.`title` AS `program_title`,
`program`.`owning_school_id`
FROM `program_year`
JOIN `program` ON `program`.`program_id` = `program_year`.`program_id`
JOIN `cohort` ON `cohort`.`program_year_id` = `program_year`.`program_year_id`
JOIN `user_x_cohort` ON `user_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
WHERE `user_x_cohort`.`user_id` = {$clean['user_id']}
AND `user_x_cohort`.`is_primary` = 0
ORDER BY `program_title`, `cohort_title`, `start_year`
EOL;

        $query = $this->db->query($sql);
        if (! $query->num_rows()) {
            return array();
        }

        $rhett = array();
        foreach ($query->result_array() as $row) {
            $rhett[] = $row;
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * This takes care of deleting the specified cohort and all of its associations.
     *
     * Transactions are assumed to be handled outside this block
     *
     * @return false if there is no cohort with the given id, otherwise true
     */
    protected function _deleteCohort ($cohortId, &$auditAtoms)
    {
        // todo audit events
        $tables = array('course_x_cohort', $this->databaseTableName);

        $this->db->where('cohort_id', $cohortId);
        $this->db->delete($tables);

        $rhett = (! $this->transactionAtomFailed());
        if ($rhett) {
            $rhett = ($this->db->affected_rows() == 0) ? false : true;
        }

        if ($rhett) {
            $auditAtoms[] = $this->auditAtom->wrapAtom($cohortId, 'cohort_id', $this->databaseTableName,
                Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
        }
        return $rhett;
    }
}
