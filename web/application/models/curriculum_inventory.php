<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO).
 * Provides various query methods that support building and managing a curriculum inventory
 * but are not primarily selecting from the "curriculum_inventory_*" tables.
 */
class Curriculum_Inventory extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Retrieves a map of courses that are linked to sequence blocks in a given curriculum inventory report.
     * @param int $reportId the report id
     * @return array an assoc array of course records, keyed off by course id.
     */
    public function getLinkedCourses ($reportId)
    {
        $rhett = array();
        $clean =array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT c.*
FROM course c
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE sb.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['course_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves a map of courses that qualify as linkable to sequence blocks in a given inventory report
     * by the matching the given year and owning school id, and that are not already linked to a given report that report.
     * @param int $year The academic year.
     * @param int $schoolId The owning school id.
     * @param int $reportId The report id.
     * @return array an assoc array of course records, keyed off by course id.
     */
    public function getLinkableCourses ($year, $schoolId, $reportId)
    {
        $rhett = array();
        $clean =array();
        $clean['year'] = (int) $year;
        $clean['school_id'] = (int) $schoolId;
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT c.*
FROM course c
WHERE c.deleted = 0
AND c.year = {$clean['year']}
AND c.owning_school_id = {$clean['school_id']}
AND c.course_id NOT IN (
    SELECT course_id
    FROM curriculum_inventory_sequence_block
    WHERE course_id IS NOT NULL
    AND report_id = {$clean['report_id']}
)
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['course_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }


    /**
     * Checks if a given course qualifies as linkable to sequence blocks in a given inventory report
     * by the matching the given year and owning school id.
     * This check excluded qualifying courses that are already linked to sequence blocks in the given report.
     * @param int $year The academic year.
     * @param int $schoolId The owning school id.
     * @param int $reportId The report id.
     * @param int $courseId The course id.
     * @return boolean TRUE if the given course qualifies as linkable, FALSE otherwise.
     */
    public function isLinkableCourse ($year, $schoolId, $reportId, $courseId)
    {
        $rhett = false;
        $clean =array();
        $clean['year'] = (int) $year;
        $clean['school_id'] = (int) $schoolId;
        $clean['report_id'] = (int) $reportId;
        $clean['course_id'] = (int) $courseId;
        $sql =<<<EOL
SELECT c.*
FROM course c
WHERE c.course_id = {$clean['course_id']}
AND c.deleted = 0
AND c.year = {$clean['year']}
AND c.owning_school_id = {$clean['school_id']}
AND c.course_id NOT IN (
    SELECT course_id
    FROM curriculum_inventory_sequence_block
    WHERE course_id IS NOT NULL
    AND report_id = {$clean['report_id']}
)
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            $rhett = true;
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves a list of events (derived from published sessions/offerings and independent learning sessions)
     * in a given curriculum inventory report.
     * @param int $reportId the report id
     * @return array
     */
    public function getEvents ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT
s.session_id AS 'event_id',
s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
ao.name AS assessment_option_name,
SUM(TIMESTAMPDIFF(MINUTE, o.start_date, o.end_date)) AS duration
FROM `session` s
LEFT JOIN offering o ON o.session_id = s.session_id AND o.deleted = 0
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
LEFT JOIN assessment_option ao ON ao.assessment_option_id = st.assessment_option_id
JOIN course c ON c.course_id = s.course_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE c.deleted = 0
AND s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND s.ilm_session_facet_id IS NULL
AND sb.report_id = {$clean['report_id']}
GROUP BY s.session_id, s.title, sd.description, method_title, is_assessment_method

UNION

SELECT
s.session_id AS 'event_id',
s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
ao.name AS assessmentoption_name,
FLOOR(sf.hours * 60) AS duration
FROM `session` s
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
LEFT JOIN assessment_option ao ON ao.assessment_option_id = st.assessment_option_id
JOIN course c ON c.course_id = s.course_id
JOIN ilm_session_facet sf ON sf.ilm_session_facet_id = s.ilm_session_facet_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE c.deleted = 0
AND s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND sb.report_id = {$clean['report_id']}
GROUP BY s.session_id, s.title, sd.description, method_title, is_assessment_method
EOL;

        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['event_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves keywords (MeSH descriptors) associated with events (sessions)
     * in a given curriculum inventory report.
     * @param int $reportId the report id
     * @return array of arrays, each sub-array representing a keyword
     */
    public function getEventKeywords ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<< EOL
SELECT
s.session_id AS 'event_id', md.mesh_descriptor_uid, md.name
FROM `session` s
JOIN course c ON c.course_id = s.course_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
JOIN session_x_mesh sxm ON sxm.session_id = s.session_id
JOIN mesh_descriptor md ON md.mesh_descriptor_uid = sxm.mesh_descriptor_uid
WHERE c.deleted = 0
AND s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND sb.report_id = {$clean['report_id']}
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
     * Retrieves a lookup map of events ('sessions') in a given curriculum inventory report,
     * grouped and keyed off by sequence block id.
     * @param int $reportId the report id
     * @return array of arrays, each sub-array containing 'event' data.
     */
    public function getEventReferencesForSequenceBlocks ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<< EOL
SELECT sb.sequence_block_id, s.session_id AS 'event_id', s.supplemental AS 'required'
FROM `session` s
JOIN `course` c ON c.course_id = s.course_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE
s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND c.deleted = 0
AND sb.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! array_key_exists($row['sequence_block_id'], $rhett)) {
                    $rhett[$row['sequence_block_id']] = array();
                }
                $rhett[$row['sequence_block_id']][] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the relations between given program-objectives and PCRS (via competencies).
     * @param array $programObjectivesId
     * @param array $pcrsIds
     * @return array
     */
    public function getProgramObjectivesToPcrsRelations (array $programObjectivesId, array $pcrsIds)
    {
        $rhett = array(
            'relations' => array(),
            'program_objective_ids' => array(),
            'pcrs_ids' => array(),
        );

        if (! count($programObjectivesId) || ! count($pcrsIds)) {
            return $rhett;
        }

        $this->db->distinct();
        $this->db->select('o.objective_id, cxam.pcrs_id');
        $this->db->from('objective o');
        $this->db->join('competency c', 'o.competency_id = c.competency_id');
        $this->db->join('competency_x_aamc_pcrs cxam', 'c.competency_id = cxam.competency_id');
        $this->db->join('aamc_pcrs am', 'am.pcrs_id = cxam.pcrs_id');
        $this->db->where_in('am.pcrs_id', $pcrsIds);
        $this->db->where_in('o.objective_id', $programObjectivesId);
        $query = $this->db->get();
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett['relations'][] = array(
                    'rel1' => $row['objective_id'],
                    'rel2' => $row['pcrs_id'],
                );
                $rhett['program_objective_ids'][] = $row['objective_id'];
                $rhett['pcrs_ids'][] = $row['pcrs_id'];
            }
            // dedupe
            $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));
            $rhett['pcrs_ids'] = array_values(array_unique($rhett['pcrs_ids']));
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the relations between given course- and program-objectives.
     * @param array $courseObjectiveIds
     * @param array $programObjectiveIds
     * @return array
     */
    public function getCourseObjectivesToProgramObjectivesRelations (array $courseObjectiveIds, array $programObjectiveIds)
    {
        $rhett = array(
            'relations' => array(),
            'course_objective_ids' => array(),
            'program_objective_ids' => array(),
        );

        if (! count($courseObjectiveIds) || ! count($programObjectiveIds)) {
            return $rhett;
        }

        $this->db->distinct();
        $this->db->select('oxo.objective_id, oxo.parent_objective_id');
        $this->db->from('objective_x_objective oxo');
        $this->db->join('course_x_objective cxo', 'cxo.objective_id = oxo.objective_id');
        $this->db->join('program_year_x_objective pyxo', 'pyxo.objective_id = oxo.parent_objective_id');
        $this->db->where_in('oxo.objective_id', $courseObjectiveIds);
        $this->db->where_in('oxo.parent_objective_id', $programObjectiveIds);
        $query = $this->db->get();
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett['relations'][] = array(
                    'rel1' => $row['parent_objective_id'],
                    'rel2' => $row['objective_id'],
                );
                $rhett['course_objective_ids'][] = $row['objective_id'];
                $rhett['program_objective_ids'][] = $row['parent_objective_id'];
            }
            // dedupe
            $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));
            $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));
        }
        $query->free_result();

        return $rhett;
    }

    /**
     * Retrieves the relations between given session- and course-objectives.
     *
     * @param array $sessionObjectiveIds
     * @param array $courseObjectiveIds
     * @return array
     */
    public function getSessionObjectivesToCourseObjectivesRelations (array $sessionObjectiveIds, array $courseObjectiveIds)
    {
        $rhett = array(
            'relations' => array(),
            'session_objective_ids' => array(),
            'course_objective_ids' => array(),
        );

        if (! count($sessionObjectiveIds) || ! count($courseObjectiveIds)) {
            return $rhett;
        }

        $this->db->distinct();
        $this->db->select('oxo.objective_id, oxo.parent_objective_id');
        $this->db->from('objective_x_objective oxo');
        $this->db->join('session_x_objective sxo', 'sxo.objective_id = oxo.objective_id');
        $this->db->join('course_x_objective cxo', 'cxo.objective_id = oxo.parent_objective_id');
        $this->db->where_in('oxo.objective_id', $sessionObjectiveIds);
        $this->db->where_in('oxo.parent_objective_id', $courseObjectiveIds);
        $query = $this->db->get();
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett['relations'][] = array(
                    'rel1' => $row['parent_objective_id'],
                    'rel2' => $row['objective_id'],
                );
                $rhett['session_objective_ids'][] = $row['objective_id'];
                $rhett['course_objective_ids'][] = $row['parent_objective_id'];
            }
            // dedupe
            $rhett['session_objective_ids'] = array_values(array_unique($rhett['session_objective_ids']));
            $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all PCRS linked to sequence blocks (via objectives and competencies) in a given inventory report.
     * @param $reportId The report id.
     * @return array A nested array of associative arrays, keyed off by 'pcrs_id'. Each sub-array represents a PCRS
     *      and is itself an associative array with values being keyed off by 'pcrs_id' and 'description'.
     */
    public function getPcrs ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT
am.pcrs_id, am.description
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
JOIN cohort co ON co.cohort_id = cxc.cohort_id
JOIN program_year py ON py.program_year_id = co.program_year_id
JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
JOIN objective o ON o.objective_id = pyxo.objective_id
JOIN competency cm ON cm.competency_id = o.competency_id AND cm.owning_school_id = p.owning_school_id
JOIN competency cm2 ON cm2.competency_id = cm.parent_competency_id
JOIN competency_x_aamc_pcrs cxm ON cxm.competency_id = cm2.competency_id
JOIN aamc_pcrs am ON am.pcrs_id = cxm.pcrs_id
WHERE
c.deleted = 0
AND py.deleted = 0
AND r.report_id = {$clean['report_id']}

UNION

SELECT
am.pcrs_id, am.description
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
JOIN cohort co ON co.cohort_id = cxc.cohort_id
JOIN program_year py ON py.program_year_id = co.program_year_id
JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
JOIN objective o ON o.objective_id = pyxo.objective_id
JOIN competency cm ON cm.competency_id = o.competency_id AND cm.owning_school_id = p.owning_school_id
JOIN competency_x_aamc_pcrs cxm ON cxm.competency_id = cm.competency_id
JOIN aamc_pcrs am ON am.pcrs_id = cxm.pcrs_id
WHERE
c.deleted = 0
AND py.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['pcrs_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all competencies (that includes sub-domains) linked to sequence blocks in a given inventory report.
     * A further constraint on owning school is applied to ensure that all retrieved competencies belong to the same
     * school as the program that is being reported on.
     * @param $reportId The report id.
     * @return array A nested array of associative arrays, keyed off by 'competency_id'. Each sub-array
     *     represents a competency and is itself an associative array with values being keyed off by 'competency_id' and 'title'.
     */
    public function getCompetencies ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
cm.*,
cm2.title AS 'parent_title'
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
JOIN cohort co ON co.cohort_id = cxc.cohort_id
JOIN program_year py ON py.program_year_id = co.program_year_id
JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
JOIN objective o ON o.objective_id = pyxo.objective_id
JOIN competency cm ON cm.competency_id = o.competency_id AND cm.owning_school_id = p.owning_school_id
LEFT JOIN competency cm2 ON cm2.competency_id = cm.parent_competency_id
WHERE
c.deleted = 0
AND py.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! array_key_exists($row['competency_id'], $rhett)) {
                    $rhett[$row['competency_id']] = array('competency_id' => $row['competency_id'], 'title' => $row['title']);
                }
                if (! array_key_exists($row['parent_competency_id'], $rhett)) {
                    $rhett[$row['parent_competency_id']] = array('competency_id' => $row['parent_competency_id'], 'title' => $row['parent_title']);
                }
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all program objectives in a given curriculum inventory report.
     * @param int $reportId The inventory report id.
     * @return array an associative array of arrays, keyed off by objective id. Each item is an associative array, containing
     *  the objective's id and title (keys: "objective_id" and "title").
     */
    public function getProgramObjectives ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
o.objective_id, o.title
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
JOIN cohort co ON co.cohort_id = cxc.cohort_id
JOIN program_year py ON py.program_year_id = co.program_year_id
JOIN program p2 ON p2.program_id = py.program_id AND p2.owning_school_id = p.owning_school_id
JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
JOIN objective o ON o.objective_id = pyxo.objective_id
WHERE
c.deleted = 0
AND py.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['objective_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all course objectives in a given curriculum inventory report.
     * @param int $reportId The inventory report id.
     * @return array an associative array of arrays, keyed off by objective id. Each item is an associative array, containing
     *  the objective's id and title (keys: "objective_id" and "title").
     */
    public function getCourseObjectives ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
o.objective_id, o.title
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_objective cxo ON cxo.course_id = c.course_id
JOIN objective o ON o.objective_id = cxo.objective_id
WHERE
c.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['objective_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all session objectives in a given curriculum inventory report.
     * @param int $reportId The inventory report id.
     * @return array an associative array of arrays, keyed off by objective id. Each item is an associative array, containing
     *  the objective's id and title (keys: "objective_id" and "title").
     */
    public function getSessionObjectives ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
o.objective_id, o.title
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN `session` s ON s.course_id = c.course_id
JOIN session_x_objective sxo ON sxo.session_id = s.session_id
JOIN objective o ON o.objective_id = sxo.objective_id
WHERE
s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND c.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['objective_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * @param int $reportId
     * @return array
     */
    public function getCompetencyObjectReferencesForEvents ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
s.session_id AS 'event_id',
so.objective_id AS 'session_objective_id',
o.objective_id as 'course_objective_id',
o2.objective_id AS 'program_objective_id'
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN `session` s ON s.course_id = c.course_id
JOIN session_x_objective sxo ON sxo.session_id = s.session_id
JOIN objective so ON so.objective_id = sxo.objective_id
LEFT JOIN objective_x_objective oxo ON oxo.objective_id = so.objective_id
LEFT JOIN course_x_objective cxo ON cxo.objective_id = oxo.parent_objective_id
LEFT JOIN objective o ON o.objective_id = cxo.objective_id
LEFT JOIN objective_x_objective oxo2 ON oxo2.objective_id = o.objective_id
LEFT JOIN objective o2 ON o2.objective_id = oxo2.parent_objective_id
WHERE
s.deleted = 0
AND s.publish_event_id IS NOT NULL
AND c.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $eventId = $row['event_id'];
                if (! array_key_exists($eventId, $rhett)) {
                    $rhett[$eventId] = array(
                        'session_objectives' => array(),
                        'course_objectives' => array(),
                        'program_objectives' => array(),
                    );
                }
                if (isset($row['session_objective_id'])
                    && ! in_array($row['session_objective_id'], $rhett[$eventId]['session_objectives'])) {
                    $rhett[$eventId]['session_objectives'][] = $row['session_objective_id'];
                }
                if (isset($row['course_objective_id'])
                    && ! in_array($row['course_objective_id'], $rhett[$eventId]['course_objectives'])) {
                    $rhett[$eventId]['course_objectives'][] = $row['course_objective_id'];
                }
                if (isset($row['program_objective_id'])
                    && ! in_array($row['program_objective_id'], $rhett[$eventId]['program_objectives'])) {
                    $rhett[$eventId]['program_objectives'][] = $row['program_objective_id'];
                }
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * @param int $reportId
     * @return array
     */
    public function getCompetencyObjectReferencesForSequenceBlocks ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<<EOL
SELECT DISTINCT
sb.sequence_block_id,
o.objective_id as 'course_objective_id',
o2.objective_id AS 'program_objective_id'
FROM
curriculum_inventory_report r
JOIN program p ON p.program_id = r.program_id
JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
JOIN course c ON c.course_id = sb.course_id
JOIN course_x_objective cxo ON cxo.course_id = c.course_id
LEFT JOIN objective o ON o.objective_id = cxo.objective_id
LEFT JOIN objective_x_objective oxo ON oxo.objective_id = o.objective_id
LEFT JOIN objective o2 ON o2.objective_id = oxo.parent_objective_id
WHERE
c.deleted = 0
AND r.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $sequenceBlockId = $row['sequence_block_id'];
                if (! array_key_exists($sequenceBlockId, $rhett)) {
                    $rhett[$sequenceBlockId] = array(
                        'course_objectives' => array(),
                        'program_objectives' => array(),
                    );
                }
                if (isset($row['course_objective_id'])
                    && ! in_array($row['course_objective_id'], $rhett[$sequenceBlockId]['course_objectives'])) {
                    $rhett[$sequenceBlockId]['course_objectives'][] = $row['course_objective_id'];
                }
                if (isset($row['program_objective_id'])
                    && ! in_array($row['program_objective_id'], $rhett[$sequenceBlockId]['program_objectives'])) {
                    $rhett[$sequenceBlockId]['program_objectives'][] = $row['program_objective_id'];
                }
            }
        }
        $query->free_result();
        return $rhett;
    }
}
