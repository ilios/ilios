<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "session" table.
 */
class Session extends Abstract_Ilios_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('session', array('session_id'));

        $this->createDBHandle();

        $this->load->model('Group', 'group', TRUE);
        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
        $this->load->model('Learning_Material', 'learningMaterial', TRUE);
        $this->load->model('Mesh', 'mesh', TRUE);
        $this->load->model('Objective', 'objective', TRUE);
        $this->load->model('Offering', 'offering', TRUE);
        $this->load->model('Session_Type', 'sessionType', TRUE);
        $this->load->model('Canned_Queries', 'cannedQueries', TRUE);
    }

    /**
     * @return this returns an array of just the matching db rows, but not the robust tree structure
     *              which would be returned by getSessionsForCourse. Used by learning materials
     *              and offering management.
     */
    public function getSimplifiedSessionsForCourse ($courseId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('course_id', $courseId);
        $queryResults = $DB->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    public function getSILM ($silmId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('ilm_session_facet_id', $silmId);
        $queryResults = $DB->get('ilm_session_facet');

        $silm = $queryResults->first_row();

        $rhett['ilm_session_facet'] = $silmId;
        $rhett['hours'] = $silm->hours;
        $rhett['due_date'] = $silm->due_date;
        $rhett['instructors'] = $this->getInstructorsForSILM($silmId);
        $rhett['learner_groups'] = $this->getLearnerGroupsForSILM($silmId);

        // the value of this key-value pair is arbitrary
        $rhett['is_silm'] = 'true';


        return $rhett;
    }

    /**
     *
     * @param unknown_type $sessionId
     */
    public function getSILMBySessionId ($sessionId)
    {
        $DB = $this->dbHandle;

        $DB->where('session_id', $sessionId);
        $queryResults = $DB->get('session');

        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();
            $silmId = $row->ilm_session_facet_id;

            if ($silmId)
                return $this->getSILM($silmId);
        }
        return null;
    }

    /**
     * returns a list of all sessions along with the course title in this format:
     * course title - session title, also returns session id
     * The list is sorted by course title, then session title
     * The list is shows only non-deleted sessions and courses
     * The list is restricted by owning school of the course
     */
    public function getSessionsWithCourseTitle() {
        $DB = $this->dbHandle;

        $queryString = 'SELECT `course`.`title` AS `course_title`, `session`.`title` AS `session_title`,
                               `course`.`start_date`, `course`.`end_date`, `session`.`session_id`
                          FROM `course`, `session`
                         WHERE `course`.`course_id` = `session`.`course_id`
                           AND `session`.`deleted` = 0
                           AND `course`.`deleted` = 0
                           AND `course`.`owning_school_id` = ' . $this->session->userdata('school_id') . '
                      ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`, `session`.`title`';

        $queryResults = $DB->query($queryString);
        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $item = array();
            $item['value'] = $row['session_id'];
            $startDate = new DateTime($row['start_date']);
            $startDate = date_format($startDate, 'm/d/Y');
            $endDate = new DateTime($row['end_date']);
            $endDate = date_format($endDate, 'm/d/Y');
            $item['display_title'] = $row['course_title'] . ' (' .$startDate . ' - ' .$endDate . ') - ' .$row['session_title'];

            array_push($items, $item);
        }

        return $items;

    }

    /**
     * Retrieves ids of users that are associated as instructors
     * with a given ILM.
     * @param int $ilmId
     * @return array list of user ids
     */
    protected function _getILMInstructorIds ($ilmId)
    {
        $ids = $this->getIdArrayFromCrossTable('ilm_session_facet_instructor',
        		'user_id', 'ilm_session_facet_id', $ilmId);
        return is_null($ids) ? array() : array_filter($ids);
    }

    /**
     * Retrieves ids of instructor-groups that are associated with a given ILM.
     * @param int $ilmId
     * @return array list of instructor group ids
     */
    protected function _getILMInstructorGroupIds ($ilmId)
    {
        $ids = $this->getIdArrayFromCrossTable('ilm_session_facet_instructor',
                'instructor_group_id', 'ilm_session_facet_id', $ilmId);
        return is_null($ids) ?  array() : array_filter($ids);
    }

    /**
     * Retrieves ids of learner groups that are associated with a given ILM.
     * @param int $ilmId
     * @return array list of learner group ids
     */
    protected function _getILMLearnerGroupIds ($ilmId)
    {
        $ids = $this->getIdArrayFromCrossTable('ilm_session_facet_learner',
                'group_id', 'ilm_session_facet_id', $ilmId);
        return is_null($ids) ? array() : array_filter($ids);
    }

    protected function getInstructorsForSILM ($silmId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('ilm_session_facet_id', $silmId);
        $queryResults = $DB->get('ilm_session_facet_instructor');

        foreach ($queryResults->result_array() as $row) {
            if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                $igRow = $this->instructorGroup->getRowForPrimaryKeyId($row['instructor_group_id']);
                array_push($rhett, $this->convertStdObjToArray($igRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($rhett, $this->convertStdObjToArray($userRow));
            }
        }

        return $rhett;
    }

    protected function getLearnerGroupsForSILM ($silmId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('ilm_session_facet_id', $silmId);
        $queryResults = $DB->get('ilm_session_facet_learner');

        foreach ($queryResults->result_array() as $row) {
            if (isset($row['group_id']) && (! is_null($row['group_id']))) {
                $groupRow = $this->group->getRowForPrimaryKeyId($row['group_id']);
                array_push($rhett, $this->convertStdObjToArray($groupRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($rhett, $this->convertStdObjToArray($userRow));
            }
        }

        return $rhett;
    }

    public function getIdsOfPublishedSessionsForCourse ($courseId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('course_id', $courseId);
        $DB->where('deleted', 0);
        $DB->where('publish_event_id !=', 'NULL');
        $queryResults = $DB->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row['session_id']);
        }

        return $rhett;
    }

    public function rolloverSession ($sessionId, $newCourseId, $rolloverOfferingsToo, $totalOffsetDays,
                                     $rolloverIsSameAcademicYear, $objectiveIdMap)
    {
        $DB = $this->dbHandle;

        $sessionRow = $this->getRowForPrimaryKeyId($sessionId);

        $newRow = array();
        $newRow['session_id'] = null;

        $newRow['title'] = $sessionRow->title;
        $newRow['attire_required'] = $sessionRow->attire_required;
        $newRow['equipment_required'] = $sessionRow->equipment_required;
        $newRow['supplemental'] = $sessionRow->supplemental;
        $newRow['course_id'] = $newCourseId;
        $newRow['session_type_id'] = $sessionRow->session_type_id;
        $newRow['deleted'] = 0;
        $newRow['published_as_tbd'] = $sessionRow->published_as_tbd;

        $newILMSessionFacetId = null;
        if ($rolloverOfferingsToo && ($sessionRow->ilm_session_facet_id != null)) {
            $newILMSessionFacetId = $this->cloneILMSessionFacet($sessionRow->ilm_session_facet_id, $totalOffsetDays);
            $newRow['ilm_session_facet_id'] = $newILMSessionFacetId;
        }
        else {
            $newRow['ilm_session_facet_id'] = null;
        }

        $DB->insert($this->databaseTableName, $newRow);
        $newSessionId = $DB->insert_id();

        if ($newSessionId > 0) {
            $sessionDescriptionRow = $this->getRow('session_description', 'session_id', $sessionId);
            if (! is_null($sessionDescriptionRow)) {
                $newRow = array();
                $newRow['session_id'] = $newSessionId;
                $newRow['description'] = $sessionDescriptionRow->description;

                $DB->insert('session_description', $newRow);
            }


            $queryString = 'SELECT copy_disciplines_from_session_to_session(' . $sessionId . ', '
                                . $newSessionId . ')';
            $DB->query($queryString);


            $queryString = 'SELECT copy_mesh_session_to_session(' . $sessionId . ', '
                                . $newSessionId . ')';
            $DB->query($queryString);


            if ($newILMSessionFacetId != null) {
                $queryString = 'SELECT copy_ilm_session_attributes_to_ilm_session('
                                    . $sessionRow->ilm_session_facet_id . ', '
                                    . $newILMSessionFacetId . ')';
            }


            $DB->where('session_id', $sessionId);
            $queryResults = $DB->get('session_learning_material');
            $learningMaterials = array();
            foreach ($queryResults->result_array() as $row) {
                array_push($learningMaterials, $row);
            }
            $lmidPairs = array();
            foreach ($learningMaterials as $learningMaterial) {
                $newRow = array();
                $newRow['session_learning_material_id'] = null;

                $newRow['session_id'] = $newSessionId;
                $newRow['learning_material_id'] = $learningMaterial['learning_material_id'];
                $newRow['notes'] = $learningMaterial['notes'];
                $newRow['required'] = $learningMaterial['required'];
                $newRow['notes_are_public'] = $learningMaterial['notes_are_public'];

                $DB->insert('session_learning_material', $newRow);
                $pair = array();
                $pair['new'] = $DB->insert_id();
                $pair['original'] = $learningMaterial['session_learning_material_id'];

                array_push($lmidPairs, $pair);
            }
            foreach ($lmidPairs as $lmidPair) {
                $queryString = 'SELECT copy_learning_material_mesh_from_session_lm_to_session_lm('
                                . $lmidPair['original'] . ', ' . $lmidPair['new'] . ')';
                $DB->query($queryString);
            }


            $this->rolloverObjectives('session_x_objective', 'session_id', $sessionId,
                                      $newSessionId, $rolloverIsSameAcademicYear, $objectiveIdMap);


            if ($rolloverOfferingsToo) {
                $offeringIds = array();

                $DB->where('session_id', $sessionId);
                $DB->where('deleted', 0);
                $queryResults = $DB->get($this->offering->getTableName());
                foreach ($queryResults->result_array() as $row) {
                    array_push($offeringIds, $row['offering_id']);
                }

                foreach ($offeringIds as $offeringId) {
                    $this->offering->rolloverOffering($offeringId, $newSessionId, $totalOffsetDays);
                }
            }
        }
    }

    protected function cloneILMSessionFacet ($ilmSessionFacetId, $offsetDays)
    {
        $DB = $this->dbHandle;

        $DB->where('ilm_session_facet_id', $ilmSessionFacetId);
        $queryResults = $DB->get('ilm_session_facet');

        if (is_null($queryResults) || ($queryResults->num_rows() == 0)) {
            return null;
        }

        $row = $queryResults->first_row();

        $newRow = array();
        $newRow['ilm_session_facet_id'] = null;

        $newRow['hours'] = $row->hours;

        $dtDueTime = new DateTime($row->due_date, new DateTimeZone('UTC'));
        $dtDueTime->add(new DateInterval('P'.$offsetDays.'D'));

        $newRow['due_date'] = $dtDueTime->format('Y-m-d');

        $DB->insert('ilm_session_facet', $newRow);

        return $DB->insert_id();
    }

    /**
     * Returns a list of un-deleted sessions associated to a given course.
     * @param int $courseId the course identifier
     * @param boolean $excludeUnpublishedSessions pass TRUE to exclude non-published sessions
     * @param boolean $excludeTBDSessions pass TRUE to exclude sessions that were published as "To Be Done"
     * @param boolean $excludeUnpublishedLearningMaterials pass TRUE to exclude un-published learning materials
     * @param boolean $sortByStartDate pass TRUE to sort session by ILM due date/first offering start date,
     *                or FALSE to sort by title
     * @return array a nested array of assoc. arrays.
     *   Each sub-array contains data under the following keys:
     *   "session_id"         ... the session record identifier [integer]
     *   "title"              ... the session title [string]
     *   "description"        ... a general description text of the session [string]
     *   "publish_event_id"   ... the id of the "published" event for this session,
     *                            a NULL value indicates that this session
     *                            has not been published yet [integer]
     *   "publish_as_tbd"     ... flag that indicates whether this session has been
     *                            semi-published as "TO BE DONE" [integer]
     *   "equipment_required" ... flag that indicates whether this session
     *                            requires special equipment [integer]
     *   "attire_required"    ... flag that indicates whether this session
     *                            requires special attire [integer]
     *   "supplemental"       ... flag that indicates whether this session
     *                            is supplemental [integer]
     *   "session_type_id"    ... session type identifier [integer]
     *   "session_type_title" ... session type title [string]
     *   "offering_count"     ... number of session offerings [integer]
     *   "learning_materials" ... learning materials [array]
     *   "ilm_facet"          ... Independent Learning Materials (ILM) facet [array]
     *   "disciplines"        ... list of disciplines [array]
     *   "mesh_terms"         ... list of MeSH-terms [array]
     *   "objectives"         ... list of session-objectives [array]
     *   "is_learner"         ... flag that indicates whether the current user
     *                            participates as "learner" (via a learner-group/session association)
     *                            in that session [boolean]
     *   "start_date"         ... the start date of the first offering or ILM due date
     */
    public function getSessionsForCourse ($courseId, $userId, $excludeUnpublishedSessions = false,
        $excludeTBDSessions = false, $excludeUnpublishedLearningMaterials = false, $sortByStartDate = false)
    {
        $rhett = array();
        $sessions = array();

        // Complete fubar due to bad modeling of the db schema.
        //
        // There is a need to sort sessions by "first occurence", which
        // could either be the oldest start date of an associated offering for a regular session,
        // or the due date associated with an ILM session.
        // there is two ways to approach this:
        //
        // 1. do this in SQL, e.g. via nested queries
        // 2. do this in code
        //
        // I decided to go with the latter since its easier to express this in code and the number
        // of records that need to be processed are expected to not exceed a few thousand, which should
        // not carry any significant memory or processing-time overhead.
        //
        // If this assumption does not hold true in the long run switch to a SQL based approach
        // for reducing the # of records by filtering/sorting them on the DB side.
        //
        // @todo fix the underlying issue in the db schema
        // [ST 11/20/2012]

        $DB = $this->dbHandle;
        $DB->select('session.*, session_type.title AS session_type_title');
        $DB->select('offering.start_date AS offering_start_date');
        $DB->select('ilm_session_facet.due_date AS ilm_due_date');
        $DB->from($this->databaseTableName);
        $DB->join('session_type', 'session.session_type_id = session_type.session_type_id', 'left');
        $DB->join('offering', 'offering.session_id = session.session_id', 'left');
        $DB->join('ilm_session_facet', 'ilm_session_facet.ilm_session_facet_id = session.ilm_session_facet_id', 'left');
        $DB->where('session.course_id', $courseId);
        $DB->where('session.deleted', 0);
        $DB->order_by('session.session_id');
        if ($excludeUnpublishedSessions) {
            $DB->where('session.publish_event_id IS NOT NULL');
        }
        if ($excludeTBDSessions) {
            $DB->where('session.published_as_tbd', 0);
        }

        $queryResults = $DB->get();

        foreach ($queryResults->result_array() as $row) {
            $session = array();

            $sessionId = $row['session_id'];

            if (array_key_exists($sessionId, $sessions)) { // dedupe
                // get the min offering start date
                if (is_null($row['ilm_session_facet_id'])) {
                    $session = $sessions[$sessionId];
                    $offeringStartDate = $row['offering_start_date'];
                    if ($offeringStartDate && ($session['start_date'] > $offeringStartDate)) {
                        $session['start_date'] = $offeringStartDate;
                        $sessions[$sessionId] = $session;
                    }
                }
            } else {
                $session['session_id'] = $sessionId;
                $session['title'] = $row['title'];
                $session['description'] = $this->getDescription($sessionId);
                $session['publish_event_id'] = $row['publish_event_id'];
                $session['published_as_tbd'] = $row['published_as_tbd'];
                $session['attire_required'] = $row['attire_required'];
                $session['equipment_required'] = $row['equipment_required'];
                $session['supplemental'] = $row['supplemental'];
                $session['session_type_id'] = $row['session_type_id'];
                $session['session_type_title'] = $row['session_type_title'];
                $session['offering_count'] = $this->getOfferingCount($sessionId);
                $session['learning_materials'] = $this->learningMaterial->getLearningMaterialsForSession($sessionId, $excludeUnpublishedLearningMaterials);
                $session['is_learner'] = $this->cannedQueries->isUserInSessionAsLearner($userId, $sessionId);

                if (! is_null($row['ilm_session_facet_id'])) {
                    $session['ilm_facet'] = $this->getIndependentLearningFacet($row['ilm_session_facet_id']);
                    $session['start_date'] = $row['ilm_due_date'];
                } else {
                    $session['session_type_title'] = $row['session_type_title'];
                    $session['start_date'] = $row['offering_start_date'];
                }

                $crossIdArray = $this->getIdArrayFromCrossTable('session_x_discipline', 'discipline_id', 'session_id', $sessionId);
                if ($crossIdArray != null) {
                    $disciplineArray = array();
                    foreach ($crossIdArray as $id) {
                        $discipline = $this->discipline->getRowForPrimaryKeyId($id);
                        if ($discipline != null) {
                            array_push($disciplineArray, $discipline);
                        } else {
                            // todo
                        }
                    }
                    $session['disciplines'] = $disciplineArray;
                }


                $session['mesh_terms'] = array();
                $crossIdArray = $this->getIdArrayFromCrossTable('session_x_mesh', 'mesh_descriptor_uid', 'session_id', $sessionId);
                if ($crossIdArray != null) {
                    foreach ($crossIdArray as $id) {
                        array_push($session['mesh_terms'], $this->mesh->getMeSHObjectForDescriptor($id));
                    }
                }


                $objectiveArray = $this->getObjectivesForSession($sessionId);
                if (count($objectiveArray) > 0) {
                    $session['objectives'] = $objectiveArray;
                }
                $sessions[$sessionId] = $session;
            }
        }

        $rhett = array_values($sessions);

        // sort the whole bunch
        if ($sortByStartDate) { // sort by start date
            usort($rhett, function ($a, $b) {
                $cmp = strcmp($a['start_date'], $b['start_date']);
                if (! $cmp) { // fallback
                    return strcmp($a['title'], $b['title']);
                }
                return $cmp;
            });
        } else { // sort by title
            usort($rhett, function ($a, $b) {
                return strcmp($a['title'], $b['title']);
            });
        }
        return $rhett;
    }

    public function getObjectivesForSession ($sessionId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('session_x_objective', 'objective_id',
                                                        'session_id', $sessionId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $objective = $this->objective->getObjective($id);

                if ($objective != null) {
                    array_push($rhett, $objective);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    /**
     * Ideally, this class would include the offering model and we wouldn't do this hand hackery
     *  but CI isn't smart enough to avoid cyclic import dependencies, and since offering already
     *  has a reference to the session model (because it needs to include session type information)
     *  we hand code this.
     */
    protected function getOfferingCount ($sessionId) {
        $DB = $this->dbHandle;

        $DB->where('session_id', $sessionId);
        $DB->where('deleted', 0);
        $DB->from('offering');

        return $DB->count_all_results();
    }

    /**
     * See getOfferingCount(...) comments as to why we don't reference the offering model to do this
     */
    protected function updateOfferingPublishStatus ($sessionId, $publishEventId, &$auditAtoms) {
        $DB = $this->dbHandle;

        $updateRow = array();
        $updateRow['publish_event_id'] = ($publishEventId == -1) ? null : $publishEventId;

        $DB->where('session_id', $sessionId);
        $DB->update('offering', $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            'offering',
                                                            Audit_Event::$UPDATE_EVENT_TYPE));
    }

    /**
     * Retrieves the description for a given session.
     * @param int $sessionId
     * @return string|NULL
     */
    public function getDescription ($sessionId)
    {
        $DB = $this->dbHandle;

        $DB->where('session_id', $sessionId);
        $queryResults = $DB->get('session_description');

        if ($queryResults->num_rows() > 0) {
            $row = $queryResults->first_row();

            return $row->description;
        }
        return null;
    }

    /**
     * Updates a given session description.
     * @param int $sessionId
     * @param string $description
     * @return boolean always TRUE
     */
    protected function _updateDescription ($sessionId, $description)
    {
    	$data = array('description' => $description);
    	$DB = $this->dbHandle;
    	$DB->where('session_id', $sessionId);
    	$DB->update('session_description', $data);

    	return true; // no way to check with certainty if update was a success.
    }

    /**
     * Deletes the description for a given session.
     * @param int $sessionId
     * @return boolean TRUE on success, FALSE otherwise
     */
    protected function _deleteDescription ($sessionId)
    {
    	$DB = $this->dbHandle;
    	$DB->where('session_id', $sessionId);
    	$DB->delete('session_description');
    	return ($DB->affected_rows() != 0);
    }

    /**
     * Adds a given session description.
     * @param int $sessionId
     * @param string $description
     * @return boolean TRUE on success, FALSE otherwise
     */
    protected function _addDescription ($sessionId, $description)
    {

        $DB = $this->dbHandle;
        $newRow = array();
        $newRow['description'] = $description;
        $newRow['session_id'] = $sessionId;
        $DB->insert('session_description', $newRow);

        return ($DB->affected_rows() != 0);
    }

    /**
     * Saves the session/disciplines associations for a given session
     * and given disciplines, taken given pre-existings associations into account.
     *
     * @param int $sessionId the session id
     * @param array $disciplines nested array of disciplines
     * @param array|NULL $associatedDisciplineIds ids of disciplines already associated with the given session
     */
    protected function _saveDisciplineAssociations ($sessionId,
            $disciplines = array(), $associatedDisciplineIds = array())
    {
        $this->_saveJoinTableAssociations('session_x_discipline', 'session_id', $sessionId,
            'discipline_id', $disciplines, $associatedDisciplineIds);
    }

    /**
     * Saves the session/mesh-term associations for a given session
     * and given mesh terms, taken given pre-existings associations into account.
     *
     * @param int $sessionId the session id
     * @param array $meshTerms nested array of mesh terms
     * @param array|NULL $associatedMeshTermIds ids of mesh terms already associated with the given session
     */
    protected function _saveMeshTermAssociations ($sessionId,
            $meshTerms = array(), $associatedMeshTermIds = array())
    {
        $this->_saveJoinTableAssociations('session_x_mesh', 'session_id', $sessionId,
            'mesh_descriptor_uid', $meshTerms, $associatedMeshTermIds);
    }

    /**
     * Transactionality is assumed to be handled outside of this method.
     */
    public function addSession ($courseId, $title, $sessionTypeId, $disciplinesArray, $meshTermArray,
                         $objectiveArray, $supplemental, $attireRequired, $equipmentRequired,
                         $publishId, $description, $learningMaterialArray, $ilmId, &$auditAtoms)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $newRow = array();
        $newRow['session_id'] = null;

        $newRow['title'] = $title;
        $newRow['publish_event_id'] = ($publishId == -1) ? null : $publishId;
        $newRow['attire_required'] = $attireRequired;
        $newRow['equipment_required'] = $equipmentRequired;
        $newRow['supplemental'] = $supplemental;
        $newRow['course_id'] = $courseId;
        $newRow['session_type_id'] = $sessionTypeId;
        $newRow['ilm_session_facet_id'] = $ilmId;
        $newRow['deleted'] = 0;

        $DB->insert($this->databaseTableName, $newRow);

        $newSessionId = $DB->insert_id();

        // MAY RETURN THIS BLOCK
        if ((! $newSessionId) || ($newSessionId < 1)) {
            $lang = $this->getLangToUse();

            $rhett['error'] = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);

            return $rhett;
        }

        array_push($auditAtoms, $this->auditEvent->wrapAtom($newSessionId, 'session_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$CREATE_EVENT_TYPE, 1));

        // associate learning materials with session
        $this->learningMaterial->saveSessionLearningMaterialAssociations($newSessionId, $learningMaterialArray,
            array(), $auditAtoms);

        // associate disciplines with new session
        $this->_saveDisciplineAssociations($newSessionId, $disciplinesArray);

        // associate mesh terms with the new session
       $this->_saveMeshTermAssociations($newSessionId, $meshTermArray);

        // @todo refactor
        $objectives = $this->saveObjectives($objectiveArray, 'session_x_objective', 'session_id', $newSessionId, $auditAtoms);

        // MAY RETURN THIS BLOCK
        if (is_null($objectives)) {
            $lang = $this->getLangToUse();

            $rhett['error']
                   = $this->i18nVendor->getI18NString('general.error.db_cross_table_insert', $lang);

            return $rhett;
        }

        //
        // handle session description
        //
        $success = true;
        if (! empty($description)) { // add new description if input given
                $success = $this->_addDescription($newSessionId, $description);
        }
        if (! $success) { // deal with failure
            $lang = $this->getLangToUse();
            $rhett['error'] = $this->i18nVendor->getI18NString('course_management.error.session_save.description', $lang);
            return $rhett;
        }

        $rhett['objectives'] = $objectives;
        $rhett['session_id'] = $newSessionId;

        return $rhett;
    }

    /**
     * Transactionality is assumed to be handled outside of this method.
     */
    public function updateSession ($sessionId, $courseId, $title, $sessionTypeId, $disciplinesArray,
                            $meshTermArray, $objectiveArray, $supplemental, $attireRequired,
                            $equipmentRequired, $publishId, $publishAsTBD, $description,
                            $learningMaterialArray, $ilmId, &$auditAtoms)
    {
        $rhett = array();

        $associatedDisciplinesIds = $this->getIdArrayFromCrossTable('session_x_discipline',
        		'discipline_id', 'session_id', $sessionId);

        $associatedMeshTermIds = $this->getIdArrayFromCrossTable('session_x_mesh',
                'mesh_descriptor_uid', 'session_id', $sessionId);

        $associatedLearningMaterialIds = $this->getIdArrayFromCrossTable('session_learning_material',
        		'learning_material_id', 'session_id', $sessionId);

        $DB = $this->dbHandle;

        $updateRow = array();

        $updateRow['title'] = $title;
        $updateRow['publish_event_id'] = ($publishId == -1) ? null : $publishId;
        $updateRow['attire_required'] = $attireRequired;
        $updateRow['equipment_required'] = $equipmentRequired;
        $updateRow['supplemental'] = $supplemental;
        $updateRow['course_id'] = $courseId;
        $updateRow['session_type_id'] = $sessionTypeId;
        $updateRow['ilm_session_facet_id'] = $ilmId;
        $updateRow['deleted'] = 0;
        $updateRow['published_as_tbd'] = $publishAsTBD;

        $DB->where('session_id', $sessionId);
        $DB->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE, 1));

        // update session/learning material associations
        $this->learningMaterial->saveSessionLearningMaterialAssociations($sessionId, $learningMaterialArray,
                $associatedLearningMaterialIds, $auditAtoms);

        // update session/discipline associations
        $this->_saveDisciplineAssociations($sessionId, $disciplinesArray, $associatedDisciplinesIds);

        // update session/mesh-term associations
        $this->_saveMeshTermAssociations($sessionId, $meshTermArray, $associatedMeshTermIds);

        // @todo refactor
        $objectives = $this->saveObjectives($objectiveArray, 'session_x_objective', 'session_id', $sessionId, $auditAtoms);

        // MAY RETURN THIS BLOCK
        if (is_null($objectives)) {
            $lang = $this->getLangToUse();

            $rhett['error']
                   = $this->i18nVendor->getI18NString('general.error.db_cross_table_insert', $lang);

            return $rhett;
        }

        //
        // handle session description
        //
        $existingDescription = $this->getDescription($sessionId); // get existing description
        $success = true;
        if (is_null($existingDescription)) { // no pre-existing description
            if (! empty($description)) { // add new description if input given
                $success = $this->_addDescription($sessionId, $description);
            }
        } else { // pre-existing description
            if (! empty($description)) { // input given - update description
                $success = $this->_updateDescription($sessionId, $description);
            } else { // no input - delete description
                $success = $this->_deleteDescription($sessionId);
            }
        }
        if (! $success) { // deal with failure
            $lang = $this->getLangToUse();
            $rhett['error'] = $this->i18nVendor->getI18NString('course_management.error.session_save.description', $lang);
            return $rhett;
        }

        $rhett['objectives'] = $objectives;
        $rhett['session_id'] = $sessionId;

        $this->updateOfferingPublishStatus($sessionId, $publishId, $auditAtoms);

        return $rhett;
    }

    /**
     * Transactionality is assumed to be handled outside of this method.
     *
     * TODO Do we want to delete ilm_session_facet table rows too? (We do not on disassociations
     *          via updateSession)
     */
    public function deleteSession ($sessionId, &$auditAtoms)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $this->learningMaterial->disassociateLearningMaterial(null, $sessionId, false, $auditAtoms);
        $tables = array('session_x_discipline', 'session_x_mesh', 'session_x_objective');

        $DB->where('session_id', $sessionId);
        $DB->delete($tables);

        $updateRow = array();
        $updateRow['deleted'] = 1;

        $DB->where('session_id', $sessionId);
        $DB->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            'session_x_discipline',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            'session_x_mesh',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            'session_x_objective',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($sessionId, 'session_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$DELETE_EVENT_TYPE, 1));

        if ($DB->affected_rows() == 0) {
            $lang = $this->getLangToUse();

            $rhett['error']  = $this->i18nVendor->getI18NString('general.error.db_delete', $lang);
        }

        return $rhett;
    }

    /**
     * @return a database query results object containing rows with names offering_id, user_id and
     *              instructor_group_id. the query results represent all instructors which are
     *              presently associated with offerings for the specified session id.
     */
    public function getInstructorsForSession ($sessionId)
    {
        $queryString
            = 'SELECT `offering_instructor`.`offering_id` AS offering_id, '
                        . '`offering_instructor`.`user_id` AS user_id, '
                        . '`offering_instructor`.`instructor_group_id` AS instructor_group_id '
                    . 'FROM `offering_instructor`, `offering`, `session` '
                    . 'WHERE `session`.`session_id` = ' . $sessionId
                            . ' AND `session`.`session_id` = `offering`.`session_id`'
                            . ' AND `offering`.`offering_id` = `offering_instructor`.`offering_id`';

        $DB = $this->dbHandle;

        return $DB->query($queryString);
    }

    /**
     * @return a database query results object containing rows with names offering_id, user_id
     *              and group_id. the query results represent all learners which are
     *              presently associated with offerings for the specified session id.
     */
    public function getLearnersForSession ($sessionId)
    {
        $queryString
            = 'SELECT `offering_learner`.`offering_id` AS offering_id, '
                        . '`offering_learner`.`user_id` AS user_id, '
                        . '`offering_learner`.`group_id` AS group_id '
                    . 'FROM `offering_learner`, `offering`, `session` '
                    . 'WHERE `session`.`session_id` = ' . $sessionId
                            . ' AND `session`.`session_id` = `offering`.`session_id`'
                            . ' AND `offering`.`offering_id` = `offering_learner`.`offering_id`';

        $DB = $this->dbHandle;

        return $DB->query($queryString);
    }

    /**
     * @return a non-associative array of strings, each of which is a MeSH descriptor associated
     *              to the session; this trivial collection is being used for immature
     *              representations of session models and inspector panes (both under OM)
     */
    public function getMeSHDescriptorsForSession ($sessionId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('session_id', $sessionId);
        $queryResults = $DB->get('session_x_mesh');

        foreach ($queryResults->result_array() as $row) {
            $descriptorRow = $this->mesh->getRowForPrimaryKeyId($row['mesh_descriptor_uid']);

            array_push($rhett, $descriptorRow->name);
        }

        return $rhett;
    }

    /**
     * @return a non-associative array of strings, each of which is an objective text associated
     *              to the session; this nearly useless collection is being used for immature
     *              representations of session models and inspector panes (both under OM)
     */
    public function getObjectiveTextsForSession ($sessionId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('session_id', $sessionId);
        $queryResults = $DB->get('session_x_objective');

        foreach ($queryResults->result_array() as $row) {
            $objectiveRow = $this->objective->getRowForPrimaryKeyId($row['objective_id']);

            array_push($rhett, $objectiveRow->title);
        }

        return $rhett;
    }

    protected function getIndependentLearningFacet ($ilmId) {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('ilm_session_facet_id', $ilmId);
        $queryResults = $DB->get('ilm_session_facet');

        $ilmRow = $queryResults->first_row();
        $rhett['ilm_session_facet_id'] = $ilmId;
        $rhett['hours'] = $ilmRow->hours;
        $rhett['due_date'] = $ilmRow->due_date;

        $DB->where('ilm_session_facet_id', $ilmId);
        $queryResults = $DB->get('ilm_session_facet_learner');
        $learnerArray = array();
        foreach ($queryResults->result_array() as $row) {
            if (isset($row['group_id']) && (! is_null($row['group_id']))) {
                $groupRow = $this->group->getRowForPrimaryKeyId($row['group_id']);
                array_push($learnerArray, $this->convertStdObjToArray($groupRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($learnerArray, $this->convertStdObjToArray($userRow));
            }
        }
        $rhett['learners'] = $learnerArray;

        $DB->where('ilm_session_facet_id', $ilmId);
        $queryResults = $DB->get('ilm_session_facet_instructor');
        $instructorArray = array();
        foreach ($queryResults->result_array() as $row) {
            if (($row['user_id'] == null) || ($row['user_id'] == '')) {
                $igRow = $this->instructorGroup->getRowForPrimaryKeyId($row['instructor_group_id']);
                array_push($instructorArray, $this->convertStdObjToArray($igRow));
            }
            else {
                $userRow = $this->user->getRowForPrimaryKeyId($row['user_id']);
                array_push($instructorArray, $this->convertStdObjToArray($userRow));
            }
        }
        $rhett['instructors'] = $instructorArray;

        return $rhett;
    }

    /**
     * Inserts or updates a given independent learning session (ILM),
     * and saves given instructor- and learner(group) associations.
     * @param int|NULL $ilmId the ILM identifier
     * @param int $hours ILM hours
     * @param string $dueDate the ILM due date
     * @param array $learnerGroupIds a list of learner group ids
     * @param array $instructors a nested array of assoc. arrays,
     *     each sub-array representing an instructor or instructor-group
     * @return boolean always TRUE
     * @todo change the $ilmId argument from "in/out" to just "in".
     *     Instead, make the ILM id the function's return value.
     */
    public function saveIndependentLearningFacet (&$ilmId, $hours, $dueDate, $learnerGroupIds, $instructors)
    {
        $DB = $this->dbHandle;

        $existingInstructorIds = array();
        $existingInstructorGroupIds = array();
        $existingLearnerGroupIds = array();

        if (($ilmId == null) || ($ilmId == -1)) { // new silm, add it
            $newRow = array();
            $newRow['ilm_session_facet_id'] = null;

            $newRow['hours'] = $hours;
            $newRow['due_date'] = $dueDate;

            $DB->insert('ilm_session_facet', $newRow);

            $ilmId = $DB->insert_id();

            if ((! $ilmId) || ($ilmId == -1)) {
                return false;
            }
        } else { // existing silm, update it
            $updateRow = array();

            $updateRow['hours'] = $hours;
            $updateRow['due_date'] = $dueDate;

            $DB->where('ilm_session_facet_id', $ilmId);
            $DB->update('ilm_session_facet', $updateRow);

            $existingInstructorIds = $this->_getILMInstructorIds($ilmId);
            $existingLearnerGroupIds = $this->_getILMLearnerGroupIds($ilmId);
            $existingInstructorGroupIds = $this->_getILMInstructorGroupIds($ilmId);
        }

        //
        // deal with ilm/learner-group and ilm/instructor-group associations
        //
        // separate instructor-groups from individual instructors
        $instructorGroups = array();
        $individualInstructors = array();
        if (! empty($instructors)) {
            foreach ($instructors as $instructor) {
                if (1 == $instructor['isGroup']) { // is individual or group?
                    $instructorGroups[] = $instructor;
                } else {
                    $individualInstructors[] = $instructor;
                }
            }
        }
        $learnerGroups = array();
        if (! empty($learnerGroupIds)) {
            //
            // KLUDGE:
            // here is where it gets stupid (again).
            //
            // break out the given list of learner group ids
            // into a nested array of assoc. arrays, where each sub-array
            // contains the group id as a key/value pair (key is "dbId").
            // the underlying boilerplate code expects its input to be formatted this way.
            // ST [4/11/2012]
            // @todo replace this loop with a call to <code>array_walk()</code> and an
            //     anonymous callback function once we go up to PHP 5.3
            foreach ($learnerGroupIds as $groupId) {
                $learnerGroups[] = array('dbId' => $groupId);
            }
        }
        // save group- and user-associations.
        $this->_saveILMLearnerGroupAssociations($ilmId, $learnerGroups, $existingLearnerGroupIds);
        $this->_saveILMInstructorAssociations($ilmId, $individualInstructors, $existingInstructorIds);
        $this->_saveILMInstructorGroupAssociations($ilmId, $instructorGroups, $existingInstructorGroupIds);
        return true;
    }

    /**
     * Saves the ILM/instructor associations for a given ILM
     * and given instructors, taken given pre-existings associations into account.
     * @param int $ilmId
     * @param array $instructors
     * @param array $associatedInstructorIds
     */
    protected function _saveILMInstructorAssociations ($ilmId, $instructors = array(),
    		$associatedInstructorIds = array())
    {
        $this->_saveJoinTableAssociations('ilm_session_facet_instructor',
                'ilm_session_facet_id', $ilmId, 'user_id',
        		$instructors, $associatedInstructorIds);
    }

    /**
     * Saves the ILM/instructor-group associations for a given ILM
     * and given instructors-groups, taken given pre-existings associations into account.
     * @param int $ilmId
     * @param array $instructorGroups
     * @param array $associatedInstructorGroupsIds
     */
    protected function _saveILMInstructorGroupAssociations ($ilmId, $instructorGroups = array(),
            $associatedInstructorGroupsIds = array())
    {
        $this->_saveJoinTableAssociations('ilm_session_facet_instructor',
                'ilm_session_facet_id', $ilmId, 'instructor_group_id',
                $instructorGroups, $associatedInstructorGroupsIds);
    }


    /**
     * Saves the ILM/learner-group associations for a given ILM
     * and given learner-groups, taken given pre-existings associations into account.
     * @param int $ilmId
     * @param array $learnerGroups
     * @param array $associatedLearnerGroupsIds
     */
    protected function _saveILMLearnerGroupAssociations ($ilmId, $learnerGroups = array(),
    		$associatedLearnerGroupsIds = array())
    {
    	$this->_saveJoinTableAssociations('ilm_session_facet_learner',
    			'ilm_session_facet_id', $ilmId, 'group_id',
    			$learnerGroups, $associatedLearnerGroupsIds);
    }
}
