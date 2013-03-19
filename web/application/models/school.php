<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object to the school table.
 */
class School extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('school', array('school_id'));
        $this->load->model('Department', 'department', true);
    }

    /**
     * Returns the Ids of all schools.
     * @param boolean $excludeDeletedSchools set to TRUE to exclude schools flagged as 'deleted'
     * @return array  a list of school ids
     */
    public function getAllSchools ($excludeDeletedSchools = true)
    {
        $rhett = array();

        if ($excludeDeletedSchools) {
            $this->db->where('deleted', 0);
        }
        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row['school_id']);
        }
        return $rhett;
    }

    /**
     * Returns all schools as associative array, keyed off by school id.
     * @param boolean $excludeDeletedSchools set to TRUE to exclude schools flagged as 'deleted'
     * @return array a nested array of school records, keyed off by school id.
     */
    public function getSchoolsMap ($excludeDeletedSchools = true)
    {
        $rhett = array();
        if ($excludeDeletedSchools) {
            $this->db->where('deleted', 0);
        }
        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            $rhett[$row['school_id']] = $row;
        }
        return $rhett;
    }


    /**
     * Retrieves a non-associative array of school objects, each object being an associative array
     * with keys 'school_id', 'title', and 'departments'. the value for the
     * 'departments' key is a non-associative array of department objects, each object
     * being an associative array with keys 'department_id' and 'title'. schools or
     * departments which have their deleted bit set will not be returned.
     * @param boolean $excludeDeletedSchools set to TRUE to exclude schools flagged as 'deleted'
     * @return array a nested array of arrays, representing a list of school objects
     */
    public function getSchoolTree ($excludeDeletedSchools = true)
    {
        $rhett = array();

        if ($excludeDeletedSchools) {
            $this->db->where('deleted', 0);
        }
        $this->db->order_by('title', 'desc');

        $queryResults = $this->db->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            $model = array();
            $model['school_id'] = $row['school_id'];
            $model['title'] = $row['title'];
            $model['departments'] = $this->department->getDepartmentsForSchoolId($row['school_id']);

            array_push($rhett, $model);
        }

        return $rhett;
    }

    /**
     * Retrieves the school that a given session is associated with.
     * @param int $sessionId the session identifier
     * @return array|boolean an associative array containing the school data or FALSE if no school could be found
     */
    public function getSchoolBySessionId ($sessionId)
    {
        $clean = array();
        $clean['session_id'] = (int) $sessionId;

        $rhett = false;
        // school/course/session
        $sql = <<< EOL
SELECT s.*
FROM `school` s
JOIN `course` c ON c.`owning_school_id` = s.`school_id`
JOIN `session` ss ON ss.`course_id` = c.`course_id`
WHERE ss.`session_id` = {$clean['session_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
        	$rhett = $query->row_array();
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the school that a given course is associated with.
     * @param int $courseId the course identifier
     * @return array|boolean an associative array containing the school data or FALSE if no school could be found
     */
    public function getSchoolByCourseId ($courseId)
    {
        $clean = array();
        $clean['course_id'] = (int) $courseId;

        $rhett = false;
        // school/course
        $sql = <<< EOL
SELECT s.*
FROM `school` s
JOIN `course` c ON c.`owning_school_id` = s.`school_id`
WHERE c.`course_id` = {$clean['course_id']}
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
        	$rhett = $query->row_array();
        }
        $query->free_result();
        return $rhett;
    }
}
