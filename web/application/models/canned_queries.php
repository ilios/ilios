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
FROM `offering_x_group` oxg
JOIN `offering` o ON o.`offering_id` = oxg.`offering_id`
JOIN `session` s ON s.`session_id` = o.`session_id`
JOIN `course` c ON c.`course_id` = s.`course_id`
WHERE oxg.`group_id` = {$clean['group_id']}
AND o.`deleted` = 0
AND s.`deleted` = 0
AND c.`deleted` = 0
AND c.`archived` = 0
UNION
SELECT c.`course_id`, c.`title`
FROM `ilm_session_facet_x_group` isfxg
JOIN `ilm_session_facet` isf ON isf.`ilm_session_facet_id` = isfxg.`ilm_session_facet_id`
JOIN `session` s ON s.`ilm_session_facet_id` = isf.`ilm_session_facet_id`
JOIN `course` c ON c.`course_id` = s.`course_id`
WHERE isfxg.`group_id` = {$clean['group_id']}
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
JOIN `offering_x_group` oxg ON oxg.`offering_id` = o.`offering_id`
JOIN `group` g ON g.`group_id` = oxg.`group_id`
WHERE
s.`deleted` = 0
AND o.`deleted` = 0
AND s.`course_id` = {$clean['course_id']}
UNION
SELECT g.`group_id`, g.`title`
FROM `session` s
JOIN `ilm_session_facet` isf ON isf.`ilm_session_facet_id` = s.`ilm_session_facet_id`
JOIN `ilm_session_facet_x_group` isfxg ON isfxg.`ilm_session_facet_id` = isf.`ilm_session_facet_id`
JOIN `group` g ON g.`group_id` = isfxg.`group_id`
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
     * Similar to <code>getSILMsForCalendar</code>, but look up additional details about the entries.
     * Returns independent learning session events with fields that are needed for the calendar feeds.
     *
     * @param int $userId
     * @param int $schoolId
     * @param array $roles an array of user-role ids
     * @param int $begin UNIX timestamp when to begin search
     * @param int $end UNIX timestamp when to end search
     * @return array
     */
    public function getSILMsDetailsForCalendarFeed ($userId, $schoolId = null, $roles = array(), $begin = null, $end = null)
    {
        // get the offerings
        $silms = $this->_getSILMsForCalendarFeed($userId, $schoolId, $roles, $begin, $end);

        // extract course/session/offering ids
        $silmIds = array();
        $courseIds =  array();
        $sessionIds = array();

        foreach ($silms as $offering) {
            $silmIds[] = $offering['ilm_session_facet_id'];
            $courseIds[] = $offering['course_id'];
            $sessionIds[] = $offering['session_id'];
        }
        $silmIds = array_unique($silmIds);
        $courseIds = array_unique($courseIds);
        $sessionIds = array_unique($sessionIds);

        // retrieve associated instructors/objectives/learning materials
        $instructors = $this->getSILMsInstructors($silmIds);
        $courseObjectives = $this->getCoursesObjectives($courseIds);
        $courseMaterials = $this->getCoursesMaterials($courseIds);
        $sessionObjectives = $this->getSessionsObjectives($sessionIds);
        $sessionMaterials = $this->getSessionsMaterials($sessionIds);

        // attach the instructors/objectives/learning materials to the appropriate offerings
        for ($i = 0, $n = count($silms); $i < $n; $i++) {
            @$offerings[$i]['instructors'] = $instructors[$silms[$i]['ilm_session_facet_id']];
            @$offerings[$i]['course_objectives'] = $courseObjectives[$silms[$i]['course_id']];
            @$offerings[$i]['course_materials'] = $courseMaterials[$silms[$i]['course_id']];
            @$offerings[$i]['session_objectives'] = $sessionObjectives[$silms[$i]['session_id']];
            @$offerings[$i]['session_materials'] = $sessionMaterials[$silms[$i]['session_id']];
        }

        return $offerings;
    }

    /**
     * Similar to <code>getOfferingsForCalendar</code>, but look up additional details about the entries.
     * Returns learning-session offerings with fields that are needed for the calendar feeds.
     *
     * @param int $userId
     * @param int $schoolId
     * @param array $roles an array of user-role ids
     * @param int $begin UNIX timestamp when to begin search
     * @param int $end UNIX timestamp when to end search
     * @return array
     */
    public function getOfferingsDetailsForCalendarFeed ($userId, $schoolId = null, $roles = array(), $begin = null, $end = null)
    {
        // get the offerings
        $offerings = $this->_getOfferingsForCalendarFeed($userId, $schoolId, $roles, $begin, $end);

        // extract course/session/offering ids
        $offeringIds = array();
        $courseIds =  array();
        $sessionIds = array();

        foreach ($offerings as $offering) {
            $offeringIds[] = $offering['offering_id'];
            $courseIds[] = $offering['course_id'];
            $sessionIds[] = $offering['session_id'];
        }
        $offeringIds = array_unique($offeringIds);
        $courseIds = array_unique($courseIds);
        $sessionIds = array_unique($sessionIds);

        // retrieve associated instructors/objectives/learning materials
        $instructors = $this->getOfferingsInstructors($offeringIds);
        $courseObjectives = $this->getCoursesObjectives($courseIds);
        $courseMaterials = $this->getCoursesMaterials($courseIds);
        $sessionObjectives = $this->getSessionsObjectives($sessionIds);
        $sessionMaterials = $this->getSessionsMaterials($sessionIds);

        // attach the instructors/objectives/learning materials to the appropriate offerings
        for ($i = 0, $n = count($offerings); $i < $n; $i++) {
            @$offerings[$i]['instructors'] = $instructors[$offerings[$i]['offering_id']];
            @$offerings[$i]['course_objectives'] = $courseObjectives[$offerings[$i]['course_id']];
            @$offerings[$i]['course_materials'] = $courseMaterials[$offerings[$i]['course_id']];
            @$offerings[$i]['session_objectives'] = $sessionObjectives[$offerings[$i]['session_id']];
            @$offerings[$i]['session_materials'] = $sessionMaterials[$offerings[$i]['session_id']];
        }

        return $offerings;
    }

    /**
     * Retrieves a list of users that are associated as instructors (directly or via instructor groups)
     * with a given list of independent learning session events.
     *
     * @param array $ilms An array of independent learning session event ids.
     * @return array An array of associative arrays. Each item contains a key/value pair of event-ids/instructor names.
     */
    public function getILMsInstructors (array $ilms)
    {
        $rhett = array();

        if (empty($offerings)) {
            return $rhett;
        }
        $ilms = implode(',', $ilms);
        $sql =<<< EOL
SELECT
  user.first_name, user.last_name,
  ilm_session_facet_x_instructor.ilm_session_facet_id
FROM ilm_session_facet_x_instructor
  JOIN user ON ilm_session_facet_x_instructor.user_id=user.user_id
WHERE ilm_session_facet_x_instructor.ilm_session_facet_id IN ({$ilms})

UNION DISTINCT

SELECT
  user.first_name, user.last_name,
  ilm_session_facet_x_instructor_group.ilm_session_facet_id
FROM ilm_session_facet_x_instructor_group
  JOIN instructor_group_x_user
    ON instructor_group_x_user.instructor_group_id = ilm_session_facet_x_instructor_group.instructor_group_id
  JOIN user ON user.user_id = instructor_group_x_user.user_id
WHERE ilm_session_facet_x_instructor_group.ilm_session_facet_id IN ({$ilms})
EOL;
        $query = $this->db->query($sql);


        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['offering_id']])) {
                    $rhett[$row['ilm_session_facet_id']] = array();
                }
                $rhett[$row['ilm_session_facet_id']][] = $row['first_name'] . ' ' . $row['last_name'];
            }
        }

        $query->free_result();

        return $rhett;
    }


    /**
     * Retrieves a list of users that are associated as instructors (directly or via instructor groups)
     * with a given list of offerings.
     *
     * @param array $offerings An array of offering ids.
     * @return array An array of associative arrays. Each item contains a key/value pair of offering-ids/instructor names.
     */
    public function getOfferingsInstructors (array $offerings)
    {
        $rhett = array();

        if (empty($offerings)) {
            return $rhett;
        }
        $offerings = implode(',', $offerings);
        $sql =<<< EOL
SELECT
user.first_name, user.last_name,
offering_x_instructor.offering_id
FROM offering_x_instructor
JOIN user ON offering_x_instructor.user_id=user.user_id
WHERE offering_x_instructor.offering_id IN ($offerings)

UNION DISTINCT

SELECT
user.first_name, user.last_name,
offering_x_instructor_group.offering_id
FROM offering_x_instructor_group
JOIN instructor_group_x_user
    ON instructor_group_x_user.instructor_group_id = offering_x_instructor_group.instructor_group_id
JOIN user ON user.user_id = instructor_group_x_user.user_id
WHERE offering_x_instructor_group.offering_id IN ($offerings)
EOL;
        $query = $this->db->query($sql);


        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['offering_id']])) {
                    $rhett[$row['offering_id']] = array();
                }
                $rhett[$row['offering_id']][] = $row['first_name'] . ' ' . $row['last_name'];
            }
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * @param array $courses An array of course ids.
     * @return array
     */
    public function getCoursesObjectives(array $courses)
    {
        $rhett = array();
        if (empty($courses)) {
            return $rhett;
        }
        $courses = implode(',', $courses);
        $sql =<<< EOL
SELECT objective.title, course.course_id
FROM course
JOIN course_x_objective ON course.course_id=course_x_objective.course_id
JOIN objective ON course_x_objective.objective_id=objective.objective_id
WHERE course.course_id IN ($courses)
EOL;
        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['course_id']])) {
                    $rhett[$row['course_id']] = array();
                }
                $rhett[$row['course_id']][] = $row['title'];
            }
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * @param array $courses An array of course ids.
     * @return array
     */
    public function getCoursesMaterials(array $courses)
    {
        $rhett = array();
        if (empty($courses)) {
            return $rhett;
        }

        $courses = implode(',', $courses);
        $sql =<<< EOL
SELECT
 learning_material.title, learning_material.description,
  learning_material.learning_material_id, learning_material.filename,
 course_learning_material.required, course_learning_material.notes,
 course.course_id
FROM course
JOIN course_learning_material
 ON course.course_id=course_learning_material.course_id
JOIN learning_material
 ON course_learning_material.learning_material_id=learning_material.learning_material_id
WHERE course.course_id IN ($courses)
EOL;
        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['course_id']])) {
                    $rhett[$row['course_id']] = array();
                }
                $rhett[$row['course_id']][] = $row;
            }
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * @param array $sessions An array of session ids.
     * @return array
     */
    public function getSessionsMaterials(array $sessions)
    {
        $rhett = array();

        if (empty($sessions)) {
            return $rhett;
        }
        $sessions = implode(',', $sessions);
        $sql =<<< EOL
SELECT
 learning_material.title, learning_material.description,
  learning_material.learning_material_id, learning_material.filename,
 session_learning_material.required, session_learning_material.notes,
 session.session_id
FROM session
JOIN session_learning_material
 ON session.session_id=session_learning_material.session_id
JOIN learning_material
 ON session_learning_material.learning_material_id=learning_material.learning_material_id
WHERE session.session_id IN ($sessions)
EOL;
        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['session_id']])) {
                    $rhett[$row['session_id']] = array();
                }
                $rhett[$row['session_id']][] = $row;
            }
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * @param array $sessions An array of session ids.
     * @return array
     */
    public function getSessionsObjectives (array $sessions)
    {
        $rhett = array();

        if (empty($sessions)) {
            return $rhett;
        }
        $sessions = implode(',', $sessions);
        $sql =<<< EOL
SELECT objective.title, session.session_id
FROM session
JOIN session_x_objective on session.session_id=session_x_objective.session_id
JOIN objective on session_x_objective.objective_id=objective.objective_id
WHERE session.session_id in ($sessions)
EOL;
        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! isset($rhett[$row['session_id']])) {
                    $rhett[$row['session_id']] = array();
                }
                $rhett[$row['session_id']][] = $row['title'];
            }
        }

        $query->free_result();

        return $rhett;
    }

    /*
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
            $sql .= " LEFT JOIN offering_x_learner ON offering_x_learner.offering_id = o.offering_id";
            $sql .= " LEFT JOIN offering_x_group ON offering_x_group.offering_id = o.offering_id";
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
                $subqueries[] = $sql . " AND `offering_x_learner`.`user_id` = {$clean['user_id']}";
                $subqueries[] = $sql . <<< EOL
 AND EXISTS (
    SELECT `group_x_user`.`user_id` FROM `group_x_user`
    WHERE `group_x_user`.`group_id` = `offering_x_group`.`group_id`
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
            $sql .= "LEFT JOIN ilm_session_facet_x_learner ON ilm_session_facet_x_learner.ilm_session_facet_id = s.ilm_session_facet_id ";
            $sql .= "LEFT JOIN ilm_session_facet_x_group ON ilm_session_facet_x_group.ilm_session_facet_id = s.ilm_session_facet_id ";
        }
        if ($faculty_role) {
            $sql .= "LEFT JOIN ilm_session_facet_x_instructor ON ilm_session_facet_x_instructor.ilm_session_facet_id = s.ilm_session_facet_id ";
            $sql .= "LEFT JOIN ilm_session_facet_x_instructor_group ON ilm_session_facet_x_instructor_group.ilm_session_facet_id = s.ilm_session_facet_id ";
        }
        if ($director_role) {
            $sql .= "LEFT JOIN course_director ON course_director.course_id = c.course_id ";
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
                $clause .= "OR ( ilm_session_facet_x_learner.user_id = $user_id "
                    . "OR EXISTS (SELECT group_x_user.user_id FROM group_x_user "
                    . "WHERE group_x_user.group_id = ilm_session_facet_x_group.group_id "
                    . "AND group_x_user.user_id = $user_id) ) ";
            }
            if ($faculty_role) {
                $clause .= "OR ( ilm_session_facet_x_instructor.user_id = $user_id "
                    ."OR EXISTS (SELECT instructor_group_x_user.user_id FROM instructor_group_x_user "
                    . "WHERE instructor_group_x_user.instructor_group_id = ilm_session_facet_x_instructor_group.instructor_group_id "
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
                        . 'FROM `course`, `ilm_session_facet_x_instructor_group`, `session` '
                        . 'WHERE (`ilm_session_facet_x_instructor_group`.`instructor_group_id` = ' . $igId . ') '
                        .       'AND (`ilm_session_facet_x_instructor_group`.`ilm_session_facet_id` '
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
                        . 'FROM `course`, `session`, `offering`, `group_x_instructor_group`, `offering_x_group` '
                        . 'WHERE (`group_x_instructor_group`.`instructor_group_id` = ' . $igId . ') '
                        .       'AND (`group_x_instructor_group`.`group_id` = `offering_x_group`.`group_id`) '
                        .       'AND (`offering_x_group`.`offering_id` = `offering`.`offering_id`) '
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


            $queryString = <<<EOL
SELECT `owning_school_id` FROM `group` JOIN `cohort` USING(`cohort_id`)
JOIN `program_year` USING(`program_year_id`) JOIN `program` USING(`program_id`)
WHERE `group_id` = {$groupId}
EOL;
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

        $queryString = "SELECT * FROM offering_x_group "
            . "JOIN group_x_user ON group_x_user.group_id = offering_x_group.group_id "
            . "WHERE offering_x_group.offering_id = {$clean['offeringId']} "
            . "AND group_x_user.user_id = {$clean['userId']}";

        $queryResults = $this->db->query($queryString);

        return !!$queryResults->num_rows;
    }

    public function isUserInSILMAsLearner ($userId, $silmId)
    {
        $clean = array();

        $clean['userId'] = (int) $userId;
        $clean['silmId'] = (int)$silmId;

        $queryString = "SELECT * FROM ilm_session_facet_x_group "
            . "JOIN group_x_user ON group_x_user.group_id = ilm_session_facet_x_group.group_id "
            . "WHERE ilm_session_facet_x_group.ilm_session_facet_id = {$clean['silmId']} "
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
JOIN  `offering_x_group` oxg ON o.`offering_id` = oxg.`offering_id`
JOIN `group_x_user` gxu ON gxu.`group_id` = oxg.`group_id`
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
JOIN `offering_x_learner` AS oxl ON o.offering_id = oxl.offering_id
JOIN `user` AS u ON oxl.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `offering` AS o ON s.session_id = o.session_id
JOIN `offering_x_group` AS oxg ON o.offering_id = oxg.offering_id
JOIN `group_x_user` AS gxu ON oxg.group_id = gxu.group_id
JOIN `user` AS u ON gxu.user_id = u.user_id

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND o.deleted = 0
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_x_learner` AS i USING( ilm_session_facet_id )
JOIN `user` AS u USING( user_id )

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_x_group` AS i USING( ilm_session_facet_id )
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
JOIN `ilm_session_facet_x_instructor` AS i USING( ilm_session_facet_id )
JOIN `user` AS u USING( user_id )

WHERE c.deleted = 0 AND c.publish_event_id IS NOT NULL
AND s.deleted = 0 AND s.publish_event_id IS NOT NULL
AND u.enabled = 1

UNION DISTINCT

SELECT c.*, u.*
FROM `course` AS c
JOIN `session` AS s ON c.course_id = s.course_id
JOIN `ilm_session_facet_x_instructor_group` AS i USING( ilm_session_facet_id )
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

    /**
     * This is function returns a given user's learning session offerings with fields that are
     * required by the calendar feed to display properly.
     * We only include some calendar filters' arguments here, only those that are reused often enough for MySQL to be able
     * to cache the query efficiently.
     *
     * @param int $userId
     * @param int $schoolId
     * @param array $roles an array of user-role ids
     * @param int $begin UNIX timestamp when to begin search
     * @param int $end UNIX timestamp when to end search
     * @return array
     * @return array An array of associative arrays. Each sub-array contains course/session/ilm-event data, keyed off by:
     *     'offering_id'              ... The offering id.
     *     'room'                     ... The location where the offering is being taught/given.
     *     'start_date'               ... The offering start date.
     *     'end_date'                 ... The offering end date.
     *     'session_id'               ... The session id.
     *     'session_title'            ... The session title.
     *     'session_type'             ... The session type.
     *     'session_type_id'          ... The session type id.
     *     'description'              ... The session description.
     *     'attire_required'          ... Flag indicating whether special attire is required for this session or not.
     *     'equipment_required'       ... Flag indicating whether special equipment is required for this session or not.
     *     'supplemental'             ... Flag indicating whether this session is supplemental or not.
     *     'published_as_tbd'         ... Flag indicating whether the session is published as "scheduled as TBD".
     *     'course_id'                ... The course id.
     *     'course_title'             ... The course title.
     *     'year'                     ... The course year.
     *     'course_level'             ... The course level.
     *     'course_published_as_tbd'  ... Flag indicating whether the course is published as "scheduled as TBD".
     */
    protected function _getOfferingsForCalendarFeed ($userId, $schoolId = null, $roles = array(), $begin = null, $end = null)
    {
        $rhett = array();

        // Sanitize input
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['user_id'] = (int) $userId;
        $clean['begin'] = (int) $begin;
        $clean['end'] = (int) $end;

        $schoolWhere = '';
        $dateWhere = '';
        $userJoins = array();
        $userWhere = array();

        if (! empty($schoolId)) {
            $schoolWhere = "AND course.owning_school_id = {$clean['school_id']}";
        }
        if (! empty($begin) && ! empty($end)) {
            $dateWhere =<<< EOL
AND offering.start_date > FROM_UNIXTIME({$clean['begin']})
AND offering.end_date < FROM_UNIXTIME({$clean['end']})
EOL;
        }

        if (in_array(User_Role::STUDENT_ROLE_ID, $roles)) {
            $userJoins[] =<<< EOL
LEFT JOIN offering_x_learner
ON offering_x_learner.offering_id = offering.offering_id AND offering_x_learner.user_id = {$clean['user_id']}
EOL;
            $userJoins[] =<<< EOL
LEFT JOIN offering_x_group
ON offering_x_group.offering_id = offering.offering_id AND offering_x_group.group_id IN (
SELECT group_id from group_x_user WHERE user_id = {$clean['user_id']}
)
EOL;
            $userWhere[] = 'offering_x_learner.offering_id IS NOT NULL';
            $userWhere[] = 'offering_x_group.offering_id IS NOT NULL';
        }
        if (in_array(User_Role::FACULTY_ROLE_ID, $roles)) {
            $userJoins[] =<<< EOL
LEFT JOIN offering_x_instructor
ON offering_x_instructor.offering_id = offering.offering_id AND offering_x_instructor.user_id = {$clean['user_id']}
EOL;
            $userJoins[] =<<< EOL
LEFT JOIN offering_x_instructor_group
ON offering_x_instructor_group.offering_id = offering.offering_id AND offering_x_instructor_group.instructor_group_id IN (
SELECT instructor_group_id FROM instructor_group_x_user WHERE user_id= {$clean['user_id']}
)
EOL;
            $userWhere[] = 'offering_x_instructor.offering_id IS NOT NULL';
            $userWhere[] = 'offering_x_instructor_group.offering_id IS NOT NULL';
        }
        if (in_array(User_Role::COURSE_DIRECTOR_ROLE_ID, $roles)) {
            $userJoins[] =<<< EOL
LEFT JOIN course_director ON course_director.course_id = course.course_id
AND course_director.user_id = {$clean['user_id']}
EOL;
            $userWhere[] = 'course_director.course_id IS NOT NULL';
        }
        // flatten arrays out
        $userWhere = 'AND (' . implode(' OR ', $userWhere) . ')';
        $userJoins = implode(' ', $userJoins);

        $sql =<<< EOL
SELECT DISTINCT
    session.title as session_title, session.attire_required,
    session.equipment_required, session.supplemental, session.session_id,
    session.published_as_tbd,
    session_description.description,
    session_type.title as session_type, session_type.session_type_id,
    offering.room, offering.start_date, offering.end_date, offering.offering_id,
    course.title as course_title, course.course_id, course.year,
    course.course_level, course.published_as_tbd AS course_published_as_tbd
FROM offering
    JOIN session ON offering.session_id = session.session_id
    JOIN session_type ON session.session_type_id = session_type.session_type_id
    LEFT JOIN session_description ON session.session_id = session_description.session_id
    JOIN course ON session.course_id = course.course_id
    $userJoins
WHERE
    offering.deleted=0 AND session.deleted=0 AND course.deleted=0
    AND course.publish_event_id IS NOT NULL
    AND session.publish_event_id IS NOT NULL
    AND course.archived = 0
    $schoolWhere
    $dateWhere
    $userWhere
ORDER BY offering.start_date ASC, offering.offering_id ASC
EOL;
        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            $rhett = $query->result_array();
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * This function returns all the independent learning sessions with fields that are
     * required by the calendar feed to display properly.  We only include some calendar filters'
     * arguments here, only those that are reused often enough for MySQL to be able to cache
     * the query efficiently.
     *
     * @param int $schoolId
     * @param int $userId
     * @param array $roles
     * @param int $begin UNIX timestamp when to begin search
     * @param int $end UNIX timestamp when to end search
     * @return array An array of associative arrays. Each sub-array contains course/session/ilm-event data, keyed off by:
     *     'ilm_session_facet_id'     ... The ILM-event id.
     *     'hours'                    ... The ILM-event duration (in hours).
     *     'due_date'                 ... The ILM-event due date.
     *     'session_id'               ... The session id.
     *     'session_title'            ... The session title.
     *     'session_type'             ... The session type.
     *     'session_type_id'          ... The session type id.
     *     'description'              ... The session description.
     *     'attire_required'          ... Flag indicating whether special attire is required for this session or not.
     *     'equipment_required'       ... Flag indicating whether special equipment is required for this session or not.
     *     'supplemental'             ... Flag indicating whether this session is supplemental or not.
     *     'published_as_tbd'         ... Flag indicating whether the session is published as "scheduled as TBD".
     *     'course_id'                ... The course id.
     *     'course_title'             ... The course title.
     *     'year'                     ... The course year.
     *     'course_level'             ... The course level.
     *     'course_published_as_tbd'  ... Flag indicating whether the course is published as "scheduled as TBD".
     */
    protected function _getSILMsForCalendarFeed ($userId, $schoolId = null, $roles = null, $begin = null, $end = null)
    {
        $rhett = array();

        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['user_id'] = (int) $userId;
        $clean['roles'] = empty($roles) ? null : (is_array($roles) ? $roles : array($roles));
        $clean['begin'] = (int) $begin;
        $clean['end'] = (int) $end;

        // SELECT clause
        $sql = "SELECT DISTINCT "
            . "s.session_id, i.ilm_session_facet_id, i.hours, i.due_date, s.title AS session_title, s.session_type_id, "
            . "s.attire_required, s.equipment_required, s.supplemental, session.session_id, "
            . "c.course_id, c.title AS course_title, c.year, c.course_level, sd.description, "
            . "s.published_as_tbd, c.published_as_tbd AS course_published_as_tbd ";

        // FROM clause
        $sql .= "FROM session s "
            . "JOIN course c ON c.course_id = s.course_id "
            . "JOIN ilm_session_facet i ON i.ilm_session_facet_id = s.ilm_session_facet_id "
            . "LEFT JOIN session_description sd ON sd.session_id = s.session_id ";

        if (in_array(User_Role::STUDENT_ROLE_ID, $roles)) {
            $sql .= "LEFT JOIN ilm_session_facet_x_learner ON ilm_session_facet_x_learner.ilm_session_facet_id = s.ilm_session_facet_id ";
            $sql .= "LEFT JOIN ilm_session_facet_x_group ON ilm_session_facet_x_group.ilm_session_facet_id = s.ilm_session_facet_id ";
        }

        if (in_array(User_Role::FACULTY_ROLE_ID, $roles)) {
            $sql .= "LEFT JOIN ilm_session_facet_x_instructor ON ilm_session_facet_x_instructor.ilm_session_facet_id = s.ilm_session_facet_id ";
            $sql .= "LEFT JOIN ilm_session_facet_x_instructor_group ON ilm_session_facet_x_instructor_group.ilm_session_facet_id = s.ilm_session_facet_id ";
        }

        if (in_array(User_Role::COURSE_DIRECTOR_ROLE_ID, $roles)) {
            $sql .= "LEFT JOIN course_director ON course_director.course_id = c.course_id ";
        }

        // WHERE clause
        $sql .= "WHERE s.deleted = 0 AND c.deleted = 0 AND c.archived = 0 ";
        $sql .= "AND s.publish_event_id IS NOT NULL AND c.publish_event_id IS NOT NULL ";

        if (! empty($schoolId)) {
            $sql .= "AND c.owning_school_id = {$clean['school_id']} ";
        }
        if (! empty($begin) && ! empty($end)) {
            $sql .= "AND i.due_date > FROM_UNIXTIME({$clean['begin']}) AND i.due_date < FROM_UNIXTIME({$clean['end']}) ";
        }

        $clause = "( 0 ";
        if (in_array(User_Role::STUDENT_ROLE_ID, $roles)) {
            $clause .= "OR ilm_session_facet_x_learner.user_id = {$clean['user_id']} "
                . "OR EXISTS (SELECT group_x_user.user_id FROM group_x_user "
                . "WHERE group_x_user.group_id = ilm_session_facet_x_group.group_id "
                . "AND group_x_user.user_id = {$clean['user_id']}) ";
        }
        if (in_array(User_Role::FACULTY_ROLE_ID, $roles)) {
            $clause .= "OR ilm_session_facet_x_instructor.user_id = {$clean['user_id']} "
                ."OR EXISTS (SELECT instructor_group_x_user.user_id FROM instructor_group_x_user "
                . "WHERE instructor_group_x_user.instructor_group_id = ilm_session_facet_x_instructor_group.instructor_group_id "
                . "AND instructor_group_x_user.user_id = {$clean['user_id']}) ";
        }
        if (in_array(User_Role::COURSE_DIRECTOR_ROLE_ID, $roles)) {
            $clause .= "OR course_director.user_id = {$clean['user_id']} ";
        }
        $clause .= " )";
        $sql .= "AND  $clause ";

        // ORDER BY clause
        $sql .= "ORDER BY i.due_date ASC, s.session_id ASC";

        $query = $this->db->query($sql);

        if (0 < $query->num_rows()) {
            $rhett = $query->result_array();
        }

        $query->free_result();

        return $rhett;
    }
}
