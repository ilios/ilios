<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the competency table.
 */
class Competency extends Ilios_Base_Model
{

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('competency', array('competency_id'));
    }

    /**
     * Get the "competency tree" for a given school.
     * @param int $schoolId the school id
     * @return array an array of top-level compentencies (including their sub-competencies)
     * associated with the given school
     */
    public function getCompetencyTree ($schoolId)
    {
        return $this->_getCompetencies(null, $schoolId);
    }

    /**
     * Recursive function that retrieves a competencies for a given parent-competency and associated school.
     * @param int|null $competencyId the parent competency id (NULL for top-level competencies)
     * @param int $owningSchoolId the associated school.
     * @return array an array of competencies, each competency is an assoc. array containing the following:
     *     'competency_id' ... competency id
     *     'title' ... the competency title
     *     'subdomains' ... array of sub-competencies
     */
    protected function _getCompetencies ($competencyId, $owningSchoolId)
    {
        $rhett = array();

        $this->db->where('parent_competency_id', $competencyId);
        $this->db->where('owning_school_id', $owningSchoolId);
        $this->db->order_by('title', 'asc');
        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            $competencyObject = array();
            $competencyObject['competency_id'] = $row['competency_id'];
            $competencyObject['title'] = $row['title'];
            if (is_null($competencyId)) {
                $competencyObject['subdomains'] = $this->_getCompetencies($row['competency_id'], $owningSchoolId);
            }
            $rhett[] = $competencyObject;
        }

        return $rhett;
    }
}
