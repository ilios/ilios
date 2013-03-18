<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) for the "course_clerkship_type" table.
 */
class Course_Clerkship_Type extends Abstract_Ilios_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('course_clerkship_type', array('course_clerkship_type_id'));
    }

    /**
     * Retrieves a list of course clerkship types.
     * @return array a list of course clerkship type records, each represented as associative array
     */
    public function getList ()
    {
        $rhett = array();

        $this->db->order_by('title');

        $result = $this->db->get($this->databaseTableName);

        foreach ($result->result_array() as $row) {
            $rhett[] = $row;
        }
        return $rhett;
    }

    /**
     * Retrieves a map of course clerkship types, keyed off by id.
     * @return array a lookup map consisting of clerkship type id/title key/value pairs.
     */
    public function getMap ()
    {
        $rhett = array();

        $this->db->order_by('title');

        $result = $this->db->get($this->databaseTableName);

        foreach ($result->result_array() as $row) {
            $rhett[$row['course_clerkship_type_id']] = $row['title'];
        }
        return $rhett;
    }
}
