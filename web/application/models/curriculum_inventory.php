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
     * Retrieves a map of courses that are linked to sequence blocks in a curriculum inventory
     * for a given program year.
     * @param int $programYearId
     * @return array an assoc array of course records, keyed off by course id.
     */
    public function getLinkedCourses ($programYearId)
    {
        $rhett = array();
        $clean =array();
        $clean['py_id'] = (int) $programYearId;
        $sql =<<<EOL
SELECT c.*
FROM course c
JOIN curriculum_inventory_sequence_block sb
ON sb.course_id = c.course_id
WHERE sb.program_year_id = {$clean['py_id']}
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
     * Retrieves a map of courses that can be linked to sequence blocks in a curriculum inventory
     * for a given program year.
     * @param int $programYearId the program year id
     * @return array an assoc array of course records, keyed off by course id.
     */
    public function getLinkableCourses ($programYearId)
    {
        $rhett = array();
        $clean =array();
        $clean['py_id'] = (int) $programYearId;
        $sql =<<<EOL
SELECT c.*
FROM course c
JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
JOIN cohort co ON co.cohort_id = cxc.cohort_id
WHERE c.deleted = 0
AND co.program_year_id = {$clean['py_id']}
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
     * Retrieves a list of events (derived from sessions/offerings and independent learning sessions)
     * in a curriculum inventory a for a given program year.
     * @param int $programYearId the program year id
     * @return array
     */
    public function getEvents ($programYearId)
    {
        $rhett = array();
        $clean = array();
        $clean['py_id'] = (int) $programYearId;
        $sql =<<<EOL
SELECT
s.session_id, s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
SUM(TIMESTAMPDIFF(HOUR, o.start_date, o.end_date)) AS duration
FROM offering o
JOIN `session` s ON s.session_id = o.session_id
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
JOIN course c ON c.course_id = s.course_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE c.deleted = 0
AND s.deleted = 0
AND s.ilm_session_facet_id IS NULL
AND sb.program_year_id = {$clean['py_id']}
GROUP BY s.session_id, s.title, sd.description, method_title, is_assessment_method

UNION

SELECT
s.session_id, s.title, sd.description, st.title AS method_title,
st.assessment AS is_assessment_method,
FLOOR(sf.hours) AS duration
FROM `session` s
LEFT JOIN session_description sd ON sd.session_id = s.session_id
JOIN session_type st ON st.session_type_id = s.session_type_id
JOIN course c ON c.course_id = s.course_id
JOIN ilm_session_facet sf ON sf.ilm_session_facet_id = s.ilm_session_facet_id
JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE c.deleted = 0
AND s.deleted = 0
AND sb.program_year_id = {$clean['py_id']}
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
     * Retrieves keywords (MeSH descriptors) associated with events (sessions) in a given program year.
     * @param int $programYearId the program year id.
     * @return an array of arrays, each sub-array representing a keyword
     */
    public function getEventKeywords ($programYearId)
    {
        $rhett = array();
        $clean = array();
        $clean['py_id'] = (int) $programYearId;
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
AND sb.program_year_id = {$clean['py_id']}
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