<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) for the "program" table.
 *
 * @todo check if class members are used, if not remove them
 */
class Program extends Abstract_Ilios_Model
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

        $this->createDBHandle();

        $this->load->model('Publish_Event', 'publishEvent', TRUE);
    }

    /**
     * @todo add code docs
     * @param string $title
     * @param string $shortTitle
     * @param int $duration
     * @param array $auditAtoms
     * @return int the program.program_id of the just added row, or 0 on failure
     */
    public function addNewProgram ($title, $shortTitle, $duration, &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $newRow = array();
        $newRow['program_id'] = null;

        $newRow['title'] = $title;
        $newRow['short_title'] = $shortTitle;
        $newRow['duration'] = $duration;
        $newRow['owning_school_id'] = $this->session->userdata('school_id');
        $newRow['deleted'] = "0";

        $DB->insert($this->databaseTableName, $newRow);

        $newId = $DB->insert_id();
        array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'program_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$CREATE_EVENT_TYPE, 1));

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
        $DB = $this->dbHandle;

        $updateValues = array();

        $updateValues['title'] = $title;
        $updateValues['short_title'] = $shortTitle;
        $updateValues['duration'] = $duration;
        $updateValues['publish_event_id'] = (($publishId > 0) ? $publishId : null);

        $DB->where('program_id', $programId);

        // todo pick up warnings -- research
        $rhett = $DB->update($this->databaseTableName, $updateValues);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($programId, 'program_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE, 1));

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

        $DB = $this->dbHandle;

        $DB->select('duration');

        $DB->where('program_id', $programId);

        $queryResults = $DB->get($this->databaseTableName);

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
            $DB = $this->dbHandle;

            $DB->where('deleted', 0);
            $DB->where('publish_event_id !=', 'NULL');
            $DB->where('owning_school_id', $schoolId);
            $results = $DB->get($this->databaseTableName);

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
        $DB = $this->dbHandle;

        $clean = array();
        $clean['title'] = $DB->escape_like_str($title);
        $clean['school_id'] = (int) $schoolId;
        $clean['uid'] = (int) $uid;

        $len = strlen($title);

        if (Abstract_Ilios_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
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
        return $DB->query($sql);
    }

    /**
     * Retrieves all programs associated with a given school and accessible by a given user.
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    protected function _getPrograms ($schoolId, $uid)
    {
        $DB = $this->dbHandle;

        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['uid'] = (int) $uid;

        $sql = 'CALL programs_with_title_restricted_by_school_for_user("%%", '
            . $clean['school_id'] . ', ' . $clean['uid'] . ')';

        return $DB->query($sql);
    }
}
