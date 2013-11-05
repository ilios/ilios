<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) providing read-only access to various sets of data in the
 * Ilios database.
 */
class Canned_Queries extends Ilios_Base_Model
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Retrieves a list of course-titles and -ids for courses that are associated with a given learner group via
     * session-offerings and independent-learning-sessions.
     * Note: Archived courses are excluded.
     * @param int $groupId The learner group id.
     * @return array A nested array of associative arrays. Each item contains a course title (key: 'title') and a course
     *      identifier (key: 'course_id').
     */
    public function getCourseIdAndTitleForLearnerGroup ($groupId)
    {
        $rhett = array();
        $clean = array();
        $clean['group_id'] = (int) $groupId;

        $sql =<<< EOL
SELECT c.`course_id`, c.`title`
FROM `offering_learner` ol
JOIN `offering` o ON o.`offering_id` = ol.`offering_id`
JOIN `session` s ON s.`session_id` = o.`session_id`
JOIN `course` c ON c.`course_id` = s.`course_id`
WHERE ol.`group_id` = {$clean['group_id']}
AND o.`deleted` = 0
AND s.`deleted` = 0
AND c.`deleted` = 0
AND c.`archived` = 0
UNION
SELECT c.`course_id`, c.`title`
FROM `ilm_session_facet_learner` isfl
JOIN `ilm_session_facet` isf ON isf.`ilm_session_facet_id` = isfl.`ilm_session_facet_id`
JOIN `session` s ON s.`ilm_session_facet_id` = isf.`ilm_session_facet_id`
JOIN `course` c ON c.`course_id` = s.`course_id`
WHERE isfl.`group_id` = {$clean['group_id']}
AND s.`deleted` = 0
AND c.`deleted` = 0
AND c.`archived` = 0
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
     * Retrieves a list of learner groups that are associated with independent learning sessions and offerings
     * in a given course.
     * @param int $courseId The course id.
     * @return array A nested array of associative arrays. Each item represents a learner group, containing the group's
     *      identifier (key: 'group_id') and the group's name (key: 'title').
     */
    public function getLearnerGroupIdAndTitleForCourse ($courseId)
    {
        $rhett = array();
        $clean = array();
        $clean['course_id'] = (int) $courseId;

        $sql =<<< EOL
SELECT g.`group_id`, g.`title`
FROM `session` s
JOIN `offering` o ON o.`session_id` = s.`session_id`
JOIN `offering_learner` ol ON ol.`offering_id` = o.`offering_id`
JOIN `group` g ON g.`group_id` = ol.`group_id`
WHERE
s.`deleted` = 0
AND o.`deleted` = 0
AND s.`course_id` = {$clean['course_id']}
UNION
SELECT g.`group_id`, g.`title`
FROM `session` s
JOIN `ilm_session_facet` isf ON isf.`ilm_session_facet_id` = s.`ilm_session_facet_id`
JOIN `ilm_session_facet_learner` isfl ON isfl.`ilm_session_facet_id` = isf.`ilm_session_facet_id`
JOIN `group` g ON g.`group_id` = isfl.`group_id`
WHERE s.`deleted` = 0
AND s.`course_id` = {$clean['course_id']}
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
     * This function returns all the learning session offerings with fields that are
     * required by the calendar to display properly.  We only include some calendar filters'
     * arguments here, only those that are reused often enough for MySQL to be able to cache
     * the query efficiently.
     *
     * Returns learning-session offerings with fields that are needed for Calendar.
     * @param int $schoolId
     * @param int $userId
     * @param array $roles an array of user-role ids
     * @param int $year
     * @param bool $includeArchived
     * @param int $lastUpdatedOffset
     * @return array
     *
     * @todo the SQL-construction in this function is a horrendous mess. clean it up.[ST 8/30/2012]
     */
    public function getOfferingsForCalendar ($schoolId, $userId = null,
            $roles = array(), $year = null, $includeArchived = false,
            $lastUpdatedOffset = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['user_id'] = (int) $userId;
        $clean['year'] = (int) $year;
        $clean['last_updated_on_offset'] = (int) $lastUpdatedOffset;
        $subqueries = array();

        $student_role = in_array(User_Role::STUDENT_ROLE_ID, $roles);
        $faculty_role = in_array(User_Role::FACULTY_ROLE_ID, $roles);
        $director_role = in_array(User_Role::COURSE_DIRECTOR_ROLE_ID, $roles);

        // SELECT clause
        $sql =<<< EOL
SELECT DISTINCT
o.offering_id, o.start_date, o.end_date, o.session_id, o.room,
c.course_id, c.title AS course_title, c.year, c.course_level,
s.session_type_id, s.title AS session_title,
st.session_type_css_class,
EOL;
        // if a negative value has been given for the "last updated offset"
        // then treat this as "off-switch" and just return FALSE
        // for the "recently_updated" value.
        // otherwise, calculate whether the given offering or its parent session
        // have been updated in the last X given days.
        // see redmine tickets #1010 and #2447
        if (0 > $clean['last_updated_on_offset']) {
            $sql .= " false AS recently_updated";
        } else {
            $sql .= " GREATEST(s.last_updated_on, o.last_updated_on) >= DATE_ADD(NOW(), INTERVAL -{$clean['last_updated_on_offset']} DAY) AS recently_updated";
        }

        if ($student_role) {   // Only include these fields for Student role
            $sql .= " , s.published_as_tbd, c.published_as_tbd AS course_published_as_tbd";
        }

        // FROM clause
        $sql .=<<< EOL
 FROM offering o
JOIN session s ON s.session_id = o.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
JOIN course c ON c.course_id = s.course_id
EOL;
        if ($student_role) {
            $sql .= " LEFT JOIN offering_learner ON offering_learner.offering_id = o.offering_id";
        }
        if ($faculty_role) {
            $sql .= " LEFT JOIN offering_x_instructor ON offering_x_instructor.offering_id = o.offering_id";
            $sql .= " LEFT JOIN offering_x_instructor_group ON offering_x_instructor_group.offering_id = o.offering_id";
        }
        if ($director_role) {
            $sql .= " LEFT JOIN course_director ON course_director.course_id = c.course_id";
        }
        // WHERE clause
        $sql .= " WHERE o.deleted = 0 AND c.deleted = 0 AND s.deleted = 0 AND c.owning_school_id = {$clean['school_id']}";

        if ($student_role) {
            $sql .= " AND s.publish_event_id IS NOT NULL AND c.publish_event_id IS NOT NULL";
        }

        if (!$includeArchived) {
            $sql .= " AND c.archived = 0";
        }
        if (!empty($clean['year'])) {
            $sql .= " AND c.year = {$clean['year']}";
        }
        if (!empty($clean['user_id'])) {
            if ($student_role) {
                $subqueries[] = $sql . " AND `offering_learner`.`user_id` = {$clean['user_id']}";
                $subqueries[] = $sql . <<< EOL
 AND EXISTS (
    SELECT `group_x_user`.`user_id` FROM `group_x_user`
    WHERE `group_x_user`.`group_id` = `offering_learner`.`group_id`
    AND `group_x_user`.`user_id` = {$clean['user_id']}
)
EOL;
            }
            if ($faculty_role) {
                $subqueries[] = $sql . " AND `offering_x_instructor`.`user_id` = {$clean['user_id']}";
                $subqueries[] = $sql . <<< EOL
 AND EXISTS (
    SELECT `instructor_group_x_user`.`user_id` FROM `instructor_group_x_user`
    WHERE `offering_x_instructor_group`.`instructor_group_id` = `instructor_group_x_user`.`instructor_group_id`
    AND `instructor_group_x_user`.`user_id` = {$clean['user_id']}
)
EOL;
            }
            if ($director_role) {
                $subqueries[] = $sql . " AND course_director.`user_id` = {$clean['user_id']}";
            }
        }

        switch (count($subqueries)) {
            case 0 :
                $sql .= " ORDER BY o.start_date ASC, o.offering_id ASC";
                break;
            case 1 :
                $sql = $subqueries[0]. " ORDER BY o.start_date ASC, o.offering_id ASC";
                break;
            default :
                if ($student_role) {
                    $sql =<<< EOL
SELECT DISTINCT d.offering_id, d.start_date, d.end_date, d.session_id, d.room,
d.course_id, d.course_title, d.year, d.course_level,
d.session_type_id, d.session_title, d.session_type_css_class, d.recently_updated,
d.published_as_tbd, d.course_published_as_tbd
FROM (
EOL;
                    $sql .= implode("\n UNION \n", $subqueries);
                    $sql .= ") AS d";
                } else {
                    $sql =<<< EOL
SELECT DISTINCT d.offering_id, d.start_date, d.end_date, d.session_id, d.room,
d.course_id, d.course_title, d.year, d.course_level,
d.session_type_id, d.session_title, d.session_type_css_class, d.recently_updated
FROM (
EOL;
                    $sql .= implode("\n UNION \n", $subqueries);
                    $sql .= ") AS d";
                }
                $sql .= " ORDER BY d.start_date ASC, d.offering_id ASC";
        }
        $queryResults = $this->db->query($sql);

        $rhett = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        $queryResults->free_result();
        return $rhett;
    }

    /**
     * This function returns all the independent learning sessions with fields that are
     * required by the calendar to display properly.  We only include some calendar filters'
     * arguments here, only those that are reused often enough for MySQL to be able to cache
     * the query efficiently.
     *
     * Returns independent-learning-session (SILM) with fields that are needed for Calendar.
     * @param int $schoolId
     * @param int $userId
     * @param array $roles
     * @param int $year
     * @param bool $includeArchived
     * @param int $lastUpdatedOffset
     * @return array
     */
    public function getSILMsForCalendar ($schoolId, $userId = null, $roles = null,
            $year = null, $includeArchived = false,
            $lastUpdatedOffset = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS)
    {

        $clean = array();
        $clean['schoolId'] = (int) $schoolId;
        $clean['userId'] = (int) $userId;
        $clean['roles'] = empty($roles) ? null : (is_array($roles) ? $roles : array($roles));
        $clean['year'] = (int) $year;
        $clean['last_updated_on_offset'] = (int) $lastUpdatedOffset;

        $student_role  = in_array(4, $clean['roles']) || in_array('4', $clean['roles']);
        $faculty_role  = in_array(3, $clean['roles']) || in_array('3', $clean['roles']);
        $director_role = in_array(1, $clean['roles']) || in_array('1', $clean['roles']);

        // SELECT clause
        $sql = "SELECT DISTINCT "
            . "s.session_id, i.due_date, s.title AS session_title, s.session_type_id, "
            . "c.course_id, c.title AS course_title, c.year, c.course_level, "
            . "\"#FFFFFF\" AS fill_color, ";

        // if a negative value has been given for the "last updated offset"
        // then treat this as "off-switch" and just return FALSE
        // for the "recently_updated" value.
        // otherwise, calculate whether the given session
        // have been updated in the last X given days.
        // see redmine tickets #1010 and #2447
        if (0 > $clean['last_updated_on_offset']) {
            $sql .= " false AS recently_updated ";
        } else {
            $sql .= "s.last_updated_on >= DATE_ADD(NOW(), INTERVAL -{$clean['last_updated_on_offset']} DAY) AS recently_updated ";
        }

        if ($student_role) {
            $sql .= ", s.published_as_tbd, c.published_as_tbd AS course_published_as_tbd ";
        }

        // FROM clause
        $sql .= "FROM session s "
            . "JOIN course c ON c.course_id = s.course_id "
            . "JOIN ilm_session_facet i ON i.ilm_session_facet_id = s.ilm_session_facet_id ";

        if ($student_role) {
            if (count($roles) > 1)
                $sql .= "LEFT ";
            $sql .= "JOIN ilm_session_facet_learner ON ilm_session_facet_learner.ilm_session_facet_id = s.ilm_session_facet_id ";
        }
        if ($faculty_role) {
            if (count($roles) > 1)
                $sql .= "LEFT ";
            $sql .= "JOIN ilm_session_facet_instructor ON ilm_session_facet_instructor.ilm_session_facet_id = s.ilm_session_facet_id ";
        }
        if ($director_role) {
            if (count($roles) > 1)
                $sql .= "LEFT ";
            $sql .= "JOIN course_director ON course_director.course_id = c.course_id ";
        }

        // WHERE clause
        $sql .= "WHERE s.deleted = 0 AND c.deleted = 0 AND c.owning_school_id = $schoolId ";

        if ($student_role) {
            $sql .= "AND s.publish_event_id IS NOT NULL AND c.publish_event_id IS NOT NULL ";
        }

        if (!$includeArchived) {
            $sql .= "AND c.archived = 0 ";
        }

        if (!empty($clean['userId'])) {
            $user_id = $clean['userId'];
            $clause = "( 0 ";
            if ($student_role) {
                $clause .= "OR ( ilm_session_facet_learner.user_id = $user_id "
                    . "OR EXISTS (SELECT group_x_user.user_id FROM group_x_user "
                    . "WHERE group_x_user.group_id = ilm_session_facet_learner.group_id "
                    . "AND group_x_user.user_id = $user_id) ) ";
            }
            if ($faculty_role) {
                $clause .= "OR ( ilm_session_facet_instructor.user_id = $user_id "
                    ."OR EXISTS (SELECT instructor_group_x_user.user_id FROM instructor_group_x_user "
                    . "WHERE instructor_group_x_user.instructor_group_id = ilm_session_facet_instructor.instructor_group_id "
                    . "AND instructor_group_x_user.user_id = $user_id) ) ";
            }
            if ($director_role) {
                $clause .= "OR course_director.user_id = $user_id ";
            }
            $clause .= " )";
            $sql .= "AND  $clause ";
        }

        if (!empty($clean['year'])) {
            $sql .= "AND c.year = {$clean['year']} ";
        }

        // ORDER BY clause
        $sql .= "ORDER BY i.due_date ASC, s.session_id ASC";

        $queryResults = $this->db->query($sql);

        $rhett = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    public function getAssociatedCoursesForInstructorGroup ($igId)
    {
        $rhett = array();
        $tmpArray = array();

        $queryString = 'SELECT DISTINCT `course`.`year`, `course`.`course_id`, `course`.`title` '
                        . 'FROM `course`, `ilm_session_facet_instructor`, `session` '
                        . 'WHERE (`ilm_session_facet_instructor`.`instructor_group_id` = ' . $igId . ') '
                        .       'AND (`ilm_session_facet_instructor`.`ilm_session_facet_id` '
                        .                                   '= `session`.`ilm_session_facet_id`) '
                        .       'AND (`session`.`course_id` = `course`.`course_id`)'
                        .       'AND (`session`.`deleted` = 0) '
                        .       'AND (`course`.`deleted` = 0) '
                        .       'AND (`course`.`archived` = 0) ';
        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
            $tmpArray[$row['course_id']] = $row;
        }

        $queryString = 'SELECT DISTINCT `course`.`year`, `course`.`course_id`, `course`.`title` '
                        . 'FROM `course`, `session`, `offering_x_instructor_group`, `offering` '
                        . 'WHERE (`offering_x_instructor_group`.`instructor_group_id` = ' . $igId . ') '
                        .       'AND (`offering_x_instructor_group`.`offering_id` = `offering`.`offering_id`) '
                        .       'AND (`offering`.`session_id` = `session`.`session_id`) '
                        .       'AND (`session`.`course_id` = `course`.`course_id`)'
                        .       'AND (`session`.`deleted` = 0) '
                        .       'AND (`course`.`deleted` = 0) '
                        .       'AND (`course`.`archived` = 0) ';
        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
            $tmpArray[$row['course_id']] = $row;
        }

        $queryString = 'SELECT DISTINCT `course`.`year`, `course`.`course_id`, `course`.`title` '
                        . 'FROM `course`, `session`, `offering`, `group_x_instructor_group`, `offering_learner` '
                        . 'WHERE (`group_x_instructor_group`.`instructor_group_id` = ' . $igId . ') '
                        .       'AND (`group_x_instructor_group`.`group_id` = `offering_learner`.`group_id`) '
                        .       'AND (`offering_learner`.`offering_id` = `offering`.`offering_id`) '
                        .       'AND (`offering`.`session_id` = `session`.`session_id`) '
                        .       'AND (`session`.`course_id` = `course`.`course_id`)'
                        .       'AND (`session`.`deleted` = 0) '
                        .       'AND (`course`.`deleted` = 0) '
                        .       'AND (`course`.`archived` = 0) ';
        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
            $tmpArray[$row['course_id']] = $row;
        }

        foreach ($tmpArray as $key => $val) {
            array_push($rhett, $val);
        }

        usort($rhett, array($this, "titledObjectComparator"));

        return $rhett;
    }

    protected function titledObjectComparator ($a, $b)
    {
        return strcmp($a['title'], $b['title']);
    }

    public function getMostRecentAuditEventsForUser ($userId, $schoolId, $eventCount = 5)
    {
        $queryString = 'SELECT `audit_event`.`time_stamp`, `audit_atom`.`table_name`, '
                        .       '`audit_atom`.`table_column`, `audit_atom`.`table_row_id`, '
                        .       '`audit_atom`.`event_type`, `audit_atom`.`audit_atom_id`  '
                        .   'FROM `audit_atom`, `audit_event` '
                        .   'WHERE (`audit_event`.`user_id` = ' . $userId . ') '
                        .           'AND (`audit_event`.`audit_event_id` = `audit_atom`.`audit_event_id`) '
                        .           'AND (`audit_atom`.`root_atom` = 1) '
                        .   'ORDER BY `audit_event`.`time_stamp` DESC '
                        .   'LIMIT ' . ($eventCount * 2);

        return $this->getMostRecentAuditEvents($queryString, $eventCount, $schoolId);
    }

    protected function getMostRecentAuditEvents ($queryString, $eventCount, $schoolId)
    {
        $rhett = array();

        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
            $auditEvent = $this->getReturnableAuditEvent($row, $schoolId);

            if (! is_null($auditEvent)) {
                array_push($rhett, $auditEvent);

                if (count($rhett) == $eventCount) {
                    return $rhett;
                }
            }
        }
        return $rhett;
    }

    protected function getReturnableAuditEvent ($auditRow, $schoolId)
    {
        $tableName = $auditRow['table_name'];
        $rhett = null;

        if (($tableName == 'alert') || ($tableName == 'user')) {
            return $rhett;
        }

        $tableColumn = $auditRow['table_column'];
        $rowId = $auditRow['table_row_id'];

        if ($tableName == 'offering') {
            $queryString = 'SELECT session_id FROM offering WHERE offering_id = ' . $rowId;

            $tableName = 'session';
            $tableColumn = 'session_id';

            $queryResults = $this->db->query($queryString);
            if ($queryResults->num_rows() == 0) {
                return $rhett;
            }
            $rowId = $queryResults->first_row()->session_id;
        }
        else if ($tableName == 'program_year') {
            $queryString = 'SELECT program_id FROM program_year WHERE program_year_id = ' . $rowId;

            $tableName = 'program';
            $tableColumn = 'program_id';

            $queryResults = $this->db->query($queryString);
            if ($queryResults->num_rows() == 0) {
                return $rhett;
            }
            $rowId = $queryResults->first_row()->program_id;
        }

        $queryString = 'SELECT `title` FROM `' . $tableName . '` '
                        .   'WHERE `' . $tableColumn . '` = ' . $rowId;

        $queryResults = $this->db->query($queryString);
        if ($queryResults->num_rows() > 0) {
            $rhett = array();

            $rhett['time_stamp'] = $auditRow['time_stamp'];
            $rhett['event_type'] = $auditRow['event_type'];
            $rhett['table_name'] = $tableName;
            $rhett['table_column'] = $tableColumn;
            $rhett['table_row_id'] = $rowId;
            $rhett['relative_url'] = $this->buildRelativeURLForAuditEvent($tableName, $rowId, $schoolId);

            $rhett['title'] = $this->displayTitleForAuditEvent($tableName, $rowId,
                                                               $queryResults->first_row()->title);
        }

        return $rhett;
    }

    protected function buildRelativeURLForAuditEvent ($tableName, $rowId, $schoolId)
    {
        if (($tableName == 'session') || ($tableName == 'course')) {
            $sessionId = null;
            $courseId = null;

            if ($tableName == 'session') {
                $sessionId = $rowId;

                $queryString = 'SELECT course_id FROM session WHERE session_id = ' . $rowId;
                $queryResults = $this->db->query($queryString);
                $courseId = $queryResults->first_row()->course_id;
            }
            else {
                $courseId = $rowId;
            }

            $queryString = 'SELECT archived, owning_school_id FROM course WHERE course_id = ' . $courseId;
            $queryResults = $this->db->query($queryString);

            if ($queryResults->first_row()->archived == 1) {
                $rhett = '';
            } elseif ($schoolId != $queryResults->first_row()->owning_school_id) {
                $rhett = '';
            } else {
                $rhett = 'course_management?course_id=' . $courseId;
                if (! is_null($sessionId)) {
                    $rhett .= '&session_id=' . $sessionId;
                }
            }

            return $rhett;
        }
        else if (($tableName == 'program_year') || ($tableName == 'program')) {
            $programId = null;

            if ($tableName == 'program_year') {
                $queryString = 'SELECT program_id FROM program_year WHERE program_year_id = '
                                    . $rowId;
                $queryResults = $this->db->query($queryString);
                $programId = $queryResults->first_row()->program_id;
            }
            else {
                $programId = $rowId;
            }
            $queryString = 'SELECT owning_school_id FROM program WHERE program_id = ' . $programId;
            $queryResults = $this->db->query($queryString);

            if ($schoolId == $queryResults->first_row()->owning_school_id) {
                return 'program_management?program_id=' . $programId;
            }
        }
        else if ($tableName == 'group') {
            $groupId = $rowId;
            $found = false;

            do {
                $this->db->where('group_id', $groupId);

                $queryResults = $this->db->get("group");
                $row = $queryResults->first_row();

                if (($row != null) && ($row->parent_group_id != null)) {
                    $groupId = $row->parent_group_id;
                } else {
                    $found = true;
                }
            }
            while (($row != null) && !$found);


            $queryString = 'SELECT owning_school_id FROM cohort_master_group JOIN cohort USING(cohort_id) JOIN program_year USING(program_year_id) JOIN program USING(program_id) WHERE group_id = '. $groupId;
            $queryResults = $this->db->query($queryString);

            if ($schoolId == $queryResults->first_row()->owning_school_id) {
                return 'group_management?group_id=' . $rowId;
            }
        }
        else if ($tableName == 'instructor_group') {
            $queryString = 'SELECT school_id FROM instructor_group WHERE instructor_group_id = ' . $rowId;
            $queryResults = $this->db->query($queryString);
            if ($schoolId == $queryResults->first_row()->school_id) {
                return 'instructor_group_management?instructor_group_id=' . $rowId;
            }
        }

        return '';
    }

    protected function displayTitleForAuditEvent ($tableName, $rowId, $rowTitleValue)
    {
        if ($tableName == 'session') {
            $queryString = 'SELECT course_id FROM session WHERE session_id = ' . $rowId;
            $queryResults = $this->db->query($queryString);
            $courseId = $queryResults->first_row()->course_id;

            $queryString = 'SELECT title FROM course WHERE course_id = ' . $courseId;
            $queryResults = $this->db->query($queryString);

            return $queryResults->first_row()->title . ' - ' . $rowTitleValue;
        }

        return $rowTitleValue;
    }

    public function getProgramsForCourseIds ($courseIdArray)
    {
        $rhett = array();

        foreach ($courseIdArray as $courseId) {
            $queryString = 'SELECT `program`.`title`, `program`.`program_id` '
                            .   'FROM `cohort`, `course_x_cohort`, `program_year`, `program` '
                            .   'WHERE (`cohort`.`cohort_id` = `course_x_cohort`.`cohort_id`) '
                            .           'AND (`course_x_cohort`.`course_id` = ' . $courseId . ') '
                            .           'AND (`cohort`.`program_year_id` = `program_year`.`program_year_id`) '
                            .           'AND (`program_year`.`program_id` = `program`.`program_id`)'
                            .           'AND (`program_year`.`deleted` = 0) '
                            .           'AND (`program_year`.`archived` = 0) ';

            $queryResults = $this->db->query($queryString);
            foreach ($queryResults->result_array() as $row) {
                $canAdd = true;

                foreach ($rhett as $program) {
                    if ($program['program_id'] == $row['program_id']) {
                        $canAdd = false;

                        break;
                    }
                }

                if ($canAdd) {
                    array_push($rhett, $row);
                }
            }
        }

        return $rhett;
    }

    public function isUserInOfferingAsLearner ($userId, $offeringId)
    {
        $clean = array();

        $clean['userId'] = (int) $userId;
        $clean['offeringId'] = (int)$offeringId;

        $queryString = "SELECT * FROM offering_learner "
            . "JOIN group_x_user ON group_x_user.group_id = offering_learner.group_id "
            . "WHERE offering_learner.offering_id = {$clean['offeringId']} "
            . "AND group_x_user.user_id = {$clean['userId']}";

        $queryResults = $this->db->query($queryString);

        return !!$queryResults->num_rows;
    }

    public function isUserInSILMAsLearner ($userId, $silmId)
    {
        $clean = array();

        $clean['userId'] = (int) $userId;
        $clean['silmId'] = (int)$silmId;

        $queryString = "SELECT * FROM ilm_session_facet_learner "
            . "JOIN group_x_user ON group_x_user.group_id = ilm_session_facet_learner.group_id "
            . "WHERE ilm_session_facet_learner.ilm_session_facet_id = {$clean['silmId']} "
            . "AND group_x_user.user_id = {$clean['userId']}";

        $queryResults = $this->db->query($queryString);

        return !!$queryResults->num_rows;
    }

    /**
     * Checks if a given user is part of a learner group
     * that is linked to a given session.
     * @param int $userId the user id
     * @param int $sessionId the session id
     * @return boolean TRUE if the user a "learner" within the context of the session, FALSE otherwise
     */
    public function isUserInSessionAsLearner ($userId , $sessionId) {
    	$clean = array();
    	$clean['session_id'] = (int) $sessionId;
    	$clean['user_id'] = (int) $userId;
    	$query = <<<EOL
SELECT u.`user_id`
FROM `session` s
JOIN `offering` o ON s.`session_id` = o.`session_id`
JOIN  `offering_learner` ol ON o.`offering_id` = ol.`offering_id`
JOIN `group_x_user` gxu ON gxu.`group_id` = ol.`group_id`
JOIN `user` u ON u.`user_id` = gxu.`user_id`
WHERE s.`session_id` = {$clean['session_id']}
AND u.`user_id` = {$clean['user_id']}
EOL;
    	$queryResults = $this->db->query($query);
    	return $queryResults->num_rows() ? true : false;
    }

    public function getCoursesAndLearners ()
    {
        $queryString = <<<EOL
SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `offering` AS o ON s.session_id = o.session_id
JOIN `offering_learner` AS ol ON o.offering_id = ol.offering_id
JOIN `user` AS u ON ol.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `offering` AS o ON s.session_id = o.session_id
JOIN `offering_learner` AS ol ON o.offering_id = ol.offering_id
JOIN `group_x_user` AS gxu ON ol.group_id = gxu.group_id
JOIN `user` AS u ON gxu.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_learner` AS i USING( ilm_session_facet_id )
JOIN `user` AS u USING( user_id )

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_learner` AS i USING( ilm_session_facet_id )
JOIN `group_x_user` AS gxu USING( group_id )
JOIN `user` AS u ON gxu.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

EOL;

        $queryResults = $this->db->query($queryString);

        return $queryResults->result_array();
    }

    public function getCoursesAndInstructors ()
    {
        $queryString = <<<EOL
SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `offering` AS o ON s.session_id = o.session_id
JOIN `offering_x_instructor` AS oi ON o.offering_id = oi.offering_id
JOIN `user` AS u ON oi.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `offering` AS o ON s.session_id = o.session_id
JOIN `offering_x_instructor_group` AS oi ON o.offering_id = oi.offering_id
JOIN `instructor_group_x_user` AS igxu ON oi.instructor_group_id = igxu.instructor_group_id
JOIN `user` AS u ON igxu.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_instructor` AS i USING( ilm_session_facet_id )
JOIN `user` AS u USING( user_id )

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_instructor` AS i USING( ilm_session_facet_id )
JOIN `instructor_group_x_user` AS igxu USING( instructor_group_id )
JOIN `user` AS u ON igxu.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

EOL;

        $queryResults = $this->db->query($queryString);

        return $queryResults->result_array();
    }

    public function getCoursesAndDirectors ()
    {
        $queryString = <<<EOL
SELECT c.*, u.*
FROM `course_director`
JOIN `course` AS c USING (course_id)
JOIN `user` AS u USING (user_id)

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND u.enabled = 1

EOL;
        $queryResults = $this->db->query($queryString);

        return $queryResults->result_array();

    }
}
