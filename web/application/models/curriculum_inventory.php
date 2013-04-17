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
        $clean['reportId'] = (int) $reportId;
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
     * Retrieves a map of courses that can be linked to sequence blocks in a curriculum inventory report
     * for a given academic year and school.
     * @param int $year the academic year
     * @param int $schoolId the owning school id
     * @return array an assoc array of course records, keyed off by course id.
     */
    public function getLinkableCourses ($year, $schoolId)
    {
        $rhett = array();
        $clean =array();
        $clean['year'] = (int) $year;
        $clean['school_id'] = (int) $schoolId;
        $sql =<<<EOL
SELECT c.*
FROM course c
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
WHERE c.deleted = 0
AND c.year = {$clean['year']}
AND c.owning_school_id = {$clean['school_id']}
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
s.session_id, s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
SUM(TIMESTAMPDIFF(MINUTE, o.start_date, o.end_date)) AS duration
FROM `session` s
LEFT JOIN offering o ON o.session_id = s.session_id AND o.deleted = 0
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
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
s.session_id, s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
FLOOR(sf.hours * 60) AS duration
FROM `session` s
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
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
                $rhett[$row['session_id']] = $row;
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
s.session_id, md.mesh_descriptor_uid, md.name
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
     * grouped and keyed off by course id.
     * @param int $reportId the report id
     * @return array of arrays, each sub-array containing 'event' data.
     */
    public function getEventReferences ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<< EOL
SELECT s.course_id, s.session_id, s.supplemental AS 'required'
FROM `session` s
JOIN `course` c ON c.course_id = s.course_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE c.deleted = 0
AND s.deleted = 0
AND sb.report_id = {$clean['report_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (! array_key_exists($row['course_id'], $rhett)) {
                    $rhett[$row['course_id']] = array();
                }
                $rhett[$row['course_id']][] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves all competencies (that includes sub-domains) linked to sequence blocks in a given inventory report.
     * A further constraint on owning school is applied to ensure that all retrieved competencies belong to the same
     * school as the program that is being reported on.
     * @param $reportId the report id
     * @return array a nested array of associative arrays, keyed off by 'competency_id'. Each sub-array
     *     represents a competency and is itself an associative array with values being keyed off by 'competency_id' and 'tittle'.
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
AND sb.report_id = {$clean['report_id']}
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
     * @param int $reportId
     * @return array
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
AND sb.report_id = {$clean['report_id']}
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
AND sb.report_id = {$clean['report_id']}
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
c.deleted = 0
AND s.deleted = 0
AND sb.report_id = {$clean['report_id']}
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
        // @todo implement/document
        return array();
    }

    /**
     * @param int $reportId
     * @return array
     */
    public function getCompetencyObjectReferencesForSequenceBlocks ($reportId)
    {
        // @todo implement/document
        return array();
    }
}
