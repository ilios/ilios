<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "program" table.
 *
 * @todo check if class members are used, if not remove them
 */
class Program extends Ilios_Base_Model
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $shortTitle;

    /**
     * @var string
     */
    protected $yearDuration;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('program', array('program_id'));
        $this->load->model('Publish_Event', 'publishEvent', TRUE);
    }

    /**
     * @todo add code docs
     * @param string $title
     * @param string $shortTitle
     * @param int $duration
     * @param int $schoolId
     * @param array $auditAtoms
     * @return int the program.program_id of the just added row, or 0 on failure
     */
    public function addNewProgram ($title, $shortTitle, $duration, $schoolId, &$auditAtoms)
    {
        $newRow = array();
        $newRow['program_id'] = null;

        $newRow['title'] = $title;
        $newRow['short_title'] = $shortTitle;
        $newRow['duration'] = $duration;
        $newRow['owning_school_id'] = $schoolId;
        $newRow['deleted'] = "0";

        $this->db->insert($this->databaseTableName, $newRow);

        $newId = $this->db->insert_id();
        $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($newId, 'program_id', $this->databaseTableName,
            Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);

        return $newId;
    }

    /**
     * @todo add code docs
     * @todo check up on return value - what is it, and is it needed?
     * @param int $programId
     * @param string $title
     * @param string $shortTitle
     * @param int $duration
     * @param int $publishId
     * @param array $auditAtoms
     * @return mixed
     */
    public function updateProgramWithId ($programId, $title, $shortTitle,
        $duration, $publishId, &$auditAtoms)
    {
        $updateValues = array();

        $updateValues['title'] = $title;
        $updateValues['short_title'] = $shortTitle;
        $updateValues['duration'] = $duration;
        $updateValues['publish_event_id'] = (($publishId > 0) ? $publishId : null);

        $this->db->where('program_id', $programId);

        // todo pick up warnings -- research
        $rhett = $this->db->update($this->databaseTableName, $updateValues);

        $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($programId, 'program_id', $this->databaseTableName,
            Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE);

        return $rhett;
    }

    /**
     * Performs a title search for programs associated with a given school and
     * accessible by a given user.
     * @param string $title the program title
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    public function getProgramsFilteredOnTitleMatch ($title, $schoolId, $uid)
    {
        if (! $title) { // get all
        	return $this->_getPrograms($schoolId, $uid);
        } else { // search
        	return $this->_searchProgramsByTitle($title, $schoolId, $uid);
        }
    }

    /**
     * @param int $programId
     * @return int -1 if there is no program for the given program id, otherwise the duration in years
     * @todo add code docs
     */
    public function getDurationForProgramWithId ($programId)
    {
        $rhett = -1;

        $this->db->select('duration');
        $this->db->where('program_id', $programId);

        $queryResults = $this->db->get($this->databaseTableName);

        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();

            $rhett = $row->duration;
        }

        return $rhett;
    }

    /**
     * Returns all published programs associated with a given school
     * @param int $schoolId the school id
     * @return array an associative array of arrays, each sub-array containing program data
     * The values is keyed off by program id
     */
    public function getAllPublishedProgramsWithSchoolId($schoolId)
    {
        $rhett = array();

        if (! empty($schoolId)) {
            $this->db->where('deleted', 0);
            $this->db->where('publish_event_id !=', 'NULL');
            $this->db->where('owning_school_id', $schoolId);
            $results = $this->db->get($this->databaseTableName);

            foreach ($results->result_array() as $row) {
                $id = $row['program_id'];

                $rhett[$id] = $row;
            }
        }
        return $rhett;
    }


    /**
     * Performs a title search for programs associated
     * with a given school and accessible by a given user.
     * @param string $title the program title
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    protected function _searchProgramsByTitle ($title, $schoolId, $uid)
    {
        $clean = array();
        $clean['title'] = $this->db->escape_like_str($title);
        $clean['school_id'] = (int) $schoolId;
        $clean['uid'] = (int) $uid;

        $len = strlen($title);

        if (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
        	// trailing wildcard search
        	$sql = 'CALL programs_with_title_restricted_by_school_for_user("'
        	    . $clean['title'] . '%", ' . $clean['school_id'] . ', '
        	    . $clean['uid'] . ')';
        } else {
        	// full wildcard search
        	$sql = 'CALL programs_with_title_restricted_by_school_for_user("%'
        	    . $clean['title'] . '%", ' . $clean['school_id'] . ', '
        	    . $clean['uid'] . ')';
        }
        return $this->db->query($sql);
    }

    /**
     * Retrieves all programs associated with a given school and accessible by a given user.
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    protected function _getPrograms ($schoolId, $uid)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['uid'] = (int) $uid;

        $sql = 'CALL programs_with_title_restricted_by_school_for_user("%%", '
            . $clean['school_id'] . ', ' . $clean['uid'] . ')';

        return $this->db->query($sql);
    }
}
