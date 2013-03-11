<?php
include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "discipine" table.
 */
class Discipline extends Abstract_Ilios_Model
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

        if (Abstract_Ilios_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
        	// trailing wildcard search
        	$sql = 'CALL disciplines_for_title_restricted_by_school('
                . '"' . $clean['title'] . '%", ' . $clean['school_id']  . ')';
        } else {
        	// full wildcard search
        	$sql = 'CALL disciplines_for_title_restricted_by_school('
                . '"%' . $clean['title'] . '%", ' . $clean['school_id']  . ')';
        }
        return $this->db->query($sql);
    }

    /**
     * Retrieves all disciplines belonging to a given school.
     * @param string $title the discipline title
     * @param int $schoolId the school id
     * @return CI_DB_result a db query result object
     */
    protected function _getDisciplines ($schoolId)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $queryString = 'CALL disciplines_for_title_restricted_by_school('
                     . '"%%", ' . $clean['school_id']  . ')';
        return $this->db->query($queryString);
    }
}
