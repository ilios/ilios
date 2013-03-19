<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "course" table.
 */
class Course extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('course', array('course_id'));

        $this->load->model('Cohort', 'cohort', TRUE);
        $this->load->model('Competency', 'competency', TRUE);
        $this->load->model('Discipline', 'discipline', TRUE);
        $this->load->model('Learning_Material', 'learningMaterial', TRUE);
        $this->load->model('Mesh', 'mesh', TRUE);
        $this->load->model('Objective', 'objective', TRUE);
        $this->load->model('Publish_Event', 'publishEvent', TRUE);
        $this->load->model('Session', 'iliosSession', TRUE);    // NOT 'session' to avoid collision
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * @todo add code docs
     * @param int $courseId
     * @param int $year
     * @return array
     */
    public function getRolloverViewForAcademicYear ($courseId, $year)
    {
        $rhett = array();

        $course = $this->getRowForPrimaryKeyId($courseId);

        $rhett['directors'] = $this->getDirectorsForCourse($courseId);
        $rhett['disciplines'] = $this->getDisciplinesForCourse($courseId);
        $rhett['objectives'] = $this->getObjectivesForCourse($courseId);

        if ($course->year == $year) {
            $rhett['cohorts'] = $this->getProgramCohortDetailsForCourse($courseId);
            $rhett['competencies'] = $this->getAppliedCompetenciesForCourse($courseId);
        }
        else {
            $rhett['different_year'] = 'true';
        }

        $rhett['mesh'] = $this->getMeshTermsForCourse($courseId);

        $rhett['session_count']
                     = count($this->iliosSession->getIdsOfPublishedSessionsForCourse($courseId));

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $courseId
     * @param int $newYear
     * @param string $startDate
     * @param string $endDate
     * @param boolean $cloneOfferingsToo
     * @param int $schoolId
     * @param array $auditAtoms
     * @return array
     */
    public function rolloverCourse ($courseId, $newYear, $startDate, $endDate, $cloneOfferingsToo,
                                    $schoolId, &$auditAtoms)
    {
        $newCourseId = -1;

        $courseRow = $this->getRowForPrimaryKeyId($courseId);
        $includeCohorts = ($courseRow->year == $newYear);

        $newRow = array();
        $newRow['course_id'] = null;

        $newRow['title'] = $courseRow->title;
        $newRow['year'] = $newYear;
        $newRow['start_date'] = $startDate . ' 12:00:00';
        $newRow['end_date'] = $endDate . ' 12:00:00';
        $newRow['owning_school_id'] = $courseRow->owning_school_id;
        $newRow['course_level'] = $courseRow->course_level;
        $newRow['deleted'] = 0;
        $newRow['locked'] = 0;
        $newRow['archived'] = 0;
        $newRow['published_as_tbd'] = 0;
        $newRow['clerkship_type_id'] = $courseRow->clerkship_type_id;

        $this->db->insert($this->databaseTableName, $newRow);
        $newCourseId = $this->db->insert_id();

        if ($newCourseId > 0) {
            array_push($auditAtoms,
                       $this->auditEvent->wrapAtom($newCourseId, 'course_id',
                                                   $this->databaseTableName,
                                                   Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));

            $dtOriginalCourseStartTime = new DateTime($courseRow->start_date);
            $dtRolledOverCourseStartTime = new DateTime($newRow['start_date']);
            $dateInterval = $dtOriginalCourseStartTime->diff($dtRolledOverCourseStartTime);
            // Use total number of days here to make sure the day of the week always matches up.
            $totalOffsetDays = $dateInterval->days;

            if ($includeCohorts) {
                $queryString = 'SELECT copy_cohorts_from_course_to_course(' . $courseId . ', '
                                . $newCourseId . ')';

                $this->db->query($queryString);
            }


            $queryString = 'SELECT copy_disciplines_from_course_to_course(' . $courseId . ', '
                                . $newCourseId . ')';
            $this->db->query($queryString);


            $queryString = 'SELECT copy_directors_from_course_to_course(' . $courseId . ', '
                                . $newCourseId . ')';
            $this->db->query($queryString);


            $queryString = 'SELECT copy_mesh_from_course_to_course(' . $courseId . ', '
                                . $newCourseId . ')';
            $this->db->query($queryString);


            $this->db->where('course_id', $courseId);
            $queryResults = $this->db->get('course_learning_material');
            $learningMaterials = array();
            foreach ($queryResults->result_array() as $row) {
                array_push($learningMaterials, $row);
            }
            $lmidPairs = array();
            foreach ($learningMaterials as $learningMaterial) {
                $newRow = array();
                $newRow['course_learning_material_id'] = null;

                $newRow['course_id'] = $newCourseId;
                $newRow['learning_material_id'] = $learningMaterial['learning_material_id'];
                $newRow['notes'] = $learningMaterial['notes'];
                $newRow['required'] = $learningMaterial['required'];
                $newRow['notes_are_public'] = $learningMaterial['notes_are_public'];

                $this->db->insert('course_learning_material', $newRow);
                $pair = array();
                $pair['new'] = $this->db->insert_id();
                $pair['original'] = $learningMaterial['course_learning_material_id'];

                array_push($lmidPairs, $pair);
            }
            foreach ($lmidPairs as $lmidPair) {
                $queryString = 'SELECT copy_learning_material_mesh_from_course_lm_to_course_lm('
                                . $lmidPair['original'] . ', ' . $lmidPair['new'] . ')';
                $this->db->query($queryString);
            }


            $objectiveIdMap
                = $this->rolloverObjectives('course_x_objective', 'course_id', $courseId,
                                            $newCourseId, $includeCohorts);


            $sessionIds = $this->iliosSession->getIdsOfPublishedSessionsForCourse($courseId);

            foreach ($sessionIds as $sessionId) {
                $this->iliosSession->rolloverSession($sessionId, $newCourseId, $cloneOfferingsToo,
                                                     $totalOffsetDays, $includeCohorts, $objectiveIdMap);
            }
        }

        $rhett = array();
        array_push($rhett, $newCourseId);
        array_push($rhett,
                  ($includeCohorts
                       || ($courseRow->owning_school_id == $schoolId)));

        return $rhett;
    }

    /**
     * Performs a title search for courses belonging to a given
     * school that a given user has access to.
     * @param string $title the course title
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    public function getCoursesFilteredOnTitleMatch ($title, $schoolId, $uid)
    {
        if (! $title) { // get all
            return $this->_getCourses($schoolId, $uid);
        } else { // search
            return $this->_searchCoursesByTitle($title, $schoolId, $uid);
        }
    }


    /**
     * Retrieves all courses belonging to a given school
     * and that a given user has access to.
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    protected function _getCourses ($schoolId, $uid)
    {
    	$clean = array();
    	$clean['school_id'] = (int) $schoolId;
    	$clean['uid'] = (int) $uid;

    	$sql  = 'CALL courses_with_title_restricted_by_school_for_user('
    	. '"%%", ' . $clean['school_id'] . ', '
    	. $clean['uid'] . ')';

    	return $this->db->query($sql);
    }

    /**
     * Performs a title search for courses belonging to a given
     * school and that a given user has access to.
     * @param string $title the course title
     * @param int $schoolId the school id
     * @param int $uid the user id
     * @return CI_DB_result a db query result object
     */
    protected function _searchCoursesByTitle ($title, $schoolId, $uid)
    {
        $clean = array();
        $clean['school_id'] = (int) $schoolId;
        $clean['uid'] = (int) $uid;
        $clean['title'] = $this->db->escape_like_str($title);

        $len = strlen($title);

        if (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
            // trailing wildcard search
            $sql  = 'CALL courses_with_title_restricted_by_school_for_user('
                . '"' . $clean['title'] . '%", ' . $clean['school_id'] . ', '
                . $clean['uid'] . ')';
        } else {
            // full wildcard search
            $sql  = 'CALL courses_with_title_restricted_by_school_for_user('
                . '"%' . $clean['title'] . '%", ' . $clean['school_id'] . ', '
                . $clean['uid'] . ')';
        }

        return $this->db->query($sql);
    }

    public function courseExistsWithTitleAndYear ($title, $year)
    {
        $this->db->where('title', $title);
        $this->db->where('year', $year);
        $queryResults = $this->db->get($this->databaseTableName);

        return ($queryResults->num_rows() > 0);
    }

    public function addNewCourse ($title, $year, $schoolId, &$auditAtoms)
    {
        $start = mktime(12, 0, 0, 9, 1, $year);
        $end = mktime(12, 0, 0, 12, 14, $year);

        $newRow = array();
        $newRow['course_id'] = null;

        $newRow['title'] = $title;
        $newRow['year'] = $year;
        $newRow['start_date'] = date('Y-m-d H:i:s', $start);
        $newRow['end_date'] = date('Y-m-d H:i:s', $end);
        $newRow['owning_school_id'] = $schoolId;
        $newRow['course_level'] = "1";
        $newRow['deleted'] = 0;
        $newRow['locked'] = 0;
        $newRow['archived'] = 0;
        $newRow['published_as_tbd'] = 0;

        $this->db->insert($this->databaseTableName, $newRow);

        $newId = $this->db->insert_id();
        array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'course_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));

        return $newId;
    }

    /**
     * Updates a given course and its associated data, such as objectives, learning materials etc.
     * Note: Transactionality is expected to be handled outside of this method.
     *
     * @param int $courseId
     * @param string $title
     * @param string $externalId
     * @param string $startDate (must be in the format Y-m-d)
     * @param string $endDate (must be in the format of Y-m-d)
     * @param int $courseLevel
     * @param array $cohortArray
     * @param array $disciplinesArray
     * @param array $directorsArray
     * @param array $meshTermArray
     * @param array $objectiveArray
     * @param array $learningMaterialArray
     * @param int $publishId
     * @param int $publishAsTDB
     * @param int $clerkshipTypeId
     * @param array $auditAtoms
     * @return array ann array with one key one key 'objectives' which has a value of an
     *    array with 0-N arrays - each with the keys 'dbId' and 'md5' - the latter
     *    being the md5 hash of the descriptive text for the objective.
     * @todo add proper error handling and error display
     */
    public function saveCourseWithId ($courseId, $title, $externalId, $startDate, $endDate, $courseLevel,
        array $cohortArray, array $disciplinesArray, array $directorsArray, array $meshTermArray,
        array $objectiveArray, array $learningMaterialArray, $publishId, $publishAsTBD,
        $clerkshipTypeId, array &$auditAtoms)
    {
        $rhett = array();

        $updateRow = array();
        $updateRow['title'] = $title;
        $updateRow['external_id'] = $externalId;
        $updateRow['start_date'] = $startDate . ' 12:00:00';
        $updateRow['end_date'] = $endDate . ' 12:00:00';
        $updateRow['publish_event_id'] = (($publishId > 0) ? $publishId : null);
        $updateRow['course_level'] = $courseLevel;
        $updateRow['published_as_tbd'] = $publishAsTBD;
        $updateRow['clerkship_type_id'] = ($clerkshipTypeId > 0) ? $clerkshipTypeId : null;
        $this->db->where('course_id', $courseId);
        $this->db->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($courseId, 'course_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::CREATE_EVENT_TYPE, 1));

        $this->performCrossTableInserts($cohortArray, 'course_x_cohort', 'cohort_id', 'course_id',
                                        $courseId, 'cohortId');

        $this->performCrossTableInserts($disciplinesArray, 'course_x_discipline', 'discipline_id',
                                        'course_id', $courseId);

        $this->performCrossTableInserts($directorsArray, 'course_director', 'user_id', 'course_id',
                                        $courseId);

        $this->performCrossTableInserts($meshTermArray, 'course_x_mesh', 'mesh_descriptor_uid',
                                        'course_id', $courseId);

        foreach ($learningMaterialArray as $key => $val) {
            $meshTerms = $val['meshTerms'];
            $notes = $val['notes'];
            $required = ($val['required'] == 'true');
            $notesArePubliclyViewable = ($val['notesArePubliclyViewable'] == 'true');

            if ((! is_null($notes)) && (strlen($notes) == 0)) {
                $notes = null;
            }

            $this->learningMaterial->associateLearningMaterial($val['dbId'], $courseId, true,
                                                               $auditAtoms, $meshTerms, $notes,
                                                               $required,
                                                               $notesArePubliclyViewable);
        }

        $rhett['objectives'] = $this->_saveObjectives($objectiveArray, 'course_x_objective',
                                                     'course_id', $courseId, $auditAtoms);

        return $rhett;
    }

    /**
     * @param shouldArchive if true, shouldLock is ignored and assumed to be true
     */
    public function lockOrArchiveCourse ($courseId, $shouldLock, $shouldArchive, &$auditAtoms)
    {
        $lockValue = ($shouldLock ? 1 : 0);
        if ($shouldArchive) {
            $lockValue = 1;
            $archiveValue = 1;
        }
        else {
            $archiveValue = 0;
        }

        $updateRow = array();
        $updateRow['locked'] = $lockValue;
        $updateRow['archived'] = $archiveValue;

        $this->db->where('course_id', $courseId);
        $this->db->update($this->databaseTableName, $updateRow);


        array_push($auditAtoms, $this->auditEvent->wrapAtom($courseId, 'course_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE, 1));
    }

    /**
     * @return an associative array of five keys (or an empty array for an invalid session id),
     *              'course_title', 'session_title', 'course_id', 'course_year', 'course_start_date'
     */
    public function getSessionAndCourseMetaForSession ($sessionId)
    {
        $rhett = array();

        $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($sessionId);
        if (! is_null($sessionRow)) {
            $rhett['session_title'] = $sessionRow->title;

            $courseRow = $this->getRowForPrimaryKeyId($sessionRow->course_id);
            $rhett['course_title'] = $courseRow->title;
            $rhett['course_id'] = $courseRow->course_id;
            $rhett['course_year'] = $courseRow->year;
            $rhett['course_start_date'] = $courseRow->start_date;
        }

        return $rhett;
    }

    // TODO error conditions?
    /*
     * @return an array of stdObj - not arrays
     */
    public function getCohortsForCourse ($courseId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_x_cohort', 'cohort_id', 'course_id',
                                                        $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $cohortModel = $this->cohort->getRowForPrimaryKeyId($id);

                array_push($rhett, $cohortModel);
            }
        }

        return $rhett;
    }

    // TODO error conditions?
    public function getProgramCohortDetailsForCourse ($courseId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_x_cohort', 'cohort_id', 'course_id',
                                                        $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $programCohortModel = $this->cohort->getProgramCohortDetailsForCohortId($id);

                array_push($rhett, $programCohortModel);
            }
        }

        return $rhett;
    }

    /**
     * Retrieves a list of all applicable program competencies that can be associated
     * with objectives of a given course.
     * @param int $courseId the course id
     * @return array a nested array of assoc. arrays, each sub-array representing a competency
     */
    public function getApplicableCompetenciesForCourse ($courseId)
    {
        $rhett = array();

        $clean = array();
        $clean['course_id'] = (int) $courseId;

        $queryString = <<<EOL
SELECT DISTINCT com.*
FROM `competency` com
JOIN  `program_year_x_competency` pyxc ON com.`competency_id` = pyxc.`competency_id`
JOIN `cohort` c ON c.`program_year_id` =  pyxc.`program_year_id`
JOIN `course_x_cohort` cxc ON cxc.`cohort_id` =  c.`cohort_id`
WHERE cxc.`course_id` = {$clean['course_id']}
ORDER BY com.`competency_id`
EOL;
        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
            $rhett[] = $row;
        }

        return $rhett;
    }

    /**
     * Retrieves a list of all program competencies that are associated
     * with objectives of a given course.
     * @param int $courseId the course id
     * @return array a nested array of assoc. arrays, each sub-array representing a competency
     */
    public function getAppliedCompetenciesForCourse ($courseId)
    {
        $rhett = array();

        $clean = array();
        $clean['course_id'] = (int) $courseId;

        $queryString = <<<EOL
SELECT DISTINCT com.*
FROM `competency` com
JOIN `objective` po ON po.`competency_id` = com.`competency_id`
JOIN `objective_x_objective` oxo ON oxo.`parent_objective_id` = po.`objective_id`
JOIN `objective` o ON  o.`objective_id` = oxo.`objective_id`
JOIN `course_x_objective` cxo ON cxo.`objective_id` = o.`objective_id`
JOIN `course` c ON c.`course_id` = cxo.`course_id`
WHERE c.`course_id` = {$clean['course_id']}
ORDER BY com.`competency_id`
EOL;
        $queryResults = $this->db->query($queryString);
        foreach ($queryResults->result_array() as $row) {
        	$rhett[] = $row;
        }

        return $rhett;
    }


    public function getDisciplinesForCourse ($courseId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_x_discipline', 'discipline_id',
                                                        'course_id', $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $discipline = $this->discipline->getRowForPrimaryKeyId($id);

                if ($discipline != null) {
                    array_push($rhett, $discipline);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    public function getCoursesForDirector ($userId, $schoolId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_director', 'course_id',
                                                        'user_id', $userId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $course = $this->getRowForPrimaryKeyId($id);

                if (($course != null) && ($course->archived == 0) && ($course->owning_school_id == $schoolId)) {
                    array_push($rhett, $course);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    public function getDirectorsForCourse ($courseId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_director', 'user_id',
                                                        'course_id', $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $user = $this->user->getRowForPrimaryKeyId($id);

                if ($user != null) {
                    array_push($rhett, $user);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    public function getMeshTermsForCourse ($courseId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_x_mesh', 'mesh_descriptor_uid',
                                                        'course_id', $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                array_push($rhett, $this->mesh->getMeSHObjectForDescriptor($id));
            }
        }

        return $rhett;
    }

    /**
     * Retrieves course-objectives for a given course.
     * @param int $courseId the course id
     * @param boolean $includeCompetencies set to TRUE to include parent competency information for objectives
     * @return array
     */
    public function getObjectivesForCourse ($courseId, $includeCompetencies = false)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('course_x_objective', 'objective_id',
                                                        'course_id', $courseId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $objective = $this->objective->getObjective($id, $includeCompetencies);

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

    public function getAllCourseTitles ($schoolId)
    {
        $retval = array();

        if (isset($schoolId)) {
            $this->db->where('deleted', 0);
            $this->db->where('publish_event_id != ', 'NULL');
            $this->db->where('archived', 0);
            $this->db->where('owning_school_id', $schoolId);

            $results = $this->db->get($this->databaseTableName);

            foreach ($results->result_array() as $row) {
                $id = $row['course_id'];
                $retval[$id] = $row['title'] . ' ' . $row['external_id'];
                $retval[$id] .= ' (' . $row['start_date'] . ' - ' . $row['end_date'] . ')';
            }
        }
        return $retval;
    }

    public function getCoursesForAcademicYear ($year, $schoolId)
    {

        $retval = array();

        if (isset($schoolId)) {
            $this->db->where('deleted', 0);
            $this->db->where('publish_event_id != ', 'NULL');
            $this->db->where('archived', 0);
            $this->db->where('owning_school_id', $schoolId);
            $this->db->where('year', $year);

            $results = $this->db->get($this->databaseTableName);

            foreach ($results->result_array() as $row) {
                $row['unique_id'] = $this->getUniqueId($row['course_id']);
                array_push($retval, $row);
            }
        }
        return $retval;
    }

    /**
     * Retrieves all courses with the given school id.
     * @param int $schoolId
     * @return array a nested array of course records
     */
    public function getCoursesWithPrimarySchoolId ($schoolId)
    {
        $rhett = array();

        $this->db->where('owning_school_id', $schoolId);
        $this->db->where('deleted', 0);
        $this->db->where('publish_event_id != ', 'NULL');

        $query = $this->db->get($this->getTableName());
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * @deprecated
     * Use <code>Ilios_CourseUtils::generateHashFromCourseId()</code> instead.
     * @param int $courseId
     * @return string
     * @see Ilios_CourseUtils::generateHashFromCourseId()
     */
    public function getUniqueId ($courseId)
    {
        return Ilios_CourseUtils::generateHashFromCourseId($courseId);
    }

    /**
     * @deprecated
     * Use <code>Ilios_CourseUtils::extractCourseIdFromHash()</code> instead.
     * @param string $uniqueId
     * @return int
     * @see Ilios_CourseUtils::extractCourseIdFromHash()
     */
    public function getCourseIdFromUniqueId ($uniqueId)
    {
        return Ilios_CourseUtils::extractCourseIdFromHash($uniqueId);
    }
}
