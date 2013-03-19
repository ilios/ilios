<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "session type" table.
 */
class Session_Type extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('session_type', array('session_type_id'));
    }

    /**
     * Retrieves a list of session type ids/titles owned by a given school as key/value pairs.
     * @param int $schoolId the id of the owning school
     * @return array the session type ids/titles
     */
    public function getSessionTypeTitles ($schoolId)
    {
        $rhett = array();

        $this->db->where('owning_school_id', $schoolId);
        $this->db->order_by('title');

        $result = $this->db->get($this->databaseTableName);

        foreach ($result->result_array() as $row) {
            $id = $row['session_type_id'];
            $title = $row['title'];

            $rhett[$id] = $title;
        }

        return $rhett;
    }

    /**
     * Retrieves a list of session types owned by a given school.
     * @param int $schoolId the id of the owning school
     * @return array a list of session types records, each represented as associative array
     */
    public function getList ($schoolId)
    {
    	$rhett = array();

        $this->db->where('owning_school_id', $schoolId);
        $this->db->order_by('title');

        $result = $this->db->get($this->databaseTableName);

        foreach ($result->result_array() as $row) {
            $rhett[] = $row;
        }
        return $rhett;
    }
}
