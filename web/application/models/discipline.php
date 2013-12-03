<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "discipline" table.
 */
class Discipline extends Ilios_Base_Model
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct('discipline', array('discipline_id'));
    }

    /**
     * Performs a title search for disciplines belonging to a given
     * school.
     * @param string $title the discipline title
     * @param int $schoolId the school id
     * @return CI_DB_result a db query result object
     */
    public function getDisciplinesFilteredOnTitleMatch ($title, $schoolId)
    {
        if (! $title) { // get all
            return $this->_getDisciplines($schoolId);
        } else { // search by title
            return $this->_searchDisciplinesByTitle($title, $schoolId);
        }
    }

    /**
     * Returns the titles of all disciplines associated with a given school.
     * * @param int $schoolId the school id
     * @return array an associative array of discipline titles, keyed off by discipline id
     */
    public function getAllDisciplineTitles ($schoolId)
    {
        $rhett = array();

        $this->db->where('owning_school_id', $schoolId);
        $this->db->order_by('title', 'asc');
        $result = $this->db->get($this->databaseTableName);

        foreach ($result->result_array() as $row) {
            $id = $row['discipline_id'];
            $title = $row['title'];

            $rhett[$id] = $title;
        }

        return $rhett;
    }

    /**
     * Performs a title search for disciplines belonging to a given
     * school.
     * @param string $title the discipline title
     * @param int $schoolId the school id
     * @return CI_DB_result a db query result object
     */
    protected function _searchDisciplinesByTitle ($title, $schoolId)
    {
        $clean = array();
        $clean['title'] = $this->db->escape_like_str($title);
        $clean['school_id'] = (int) $schoolId;

        $len = strlen($title);

        $sql = "SELECT * FROM discipline WHERE owning_school_id = {$clean['school_id']}";

        if (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
            // trailing wildcard search
            $sql .= " AND title LIKE '{$clean['title']}%'";
        } else {
            // full wildcard search
            $sql .= " AND title LIKE '%{$clean['title']}%'";
        }
        return $this->db->query($sql);
    }

    /**
     * Retrieves all disciplines belonging to a given school.
     * @param int $schoolId the school id
     * @return CI_DB_result a db query result object
     */
    protected function _getDisciplines ($schoolId)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $sql = "SELECT * FROM discipline WHERE owning_school_id = {$clean['school_id']} ORDER BY title";
        return $this->db->query($sql);
    }
}
