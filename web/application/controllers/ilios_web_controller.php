<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_base_controller.php';

/**
 * @package Ilios
 *
 * Base "Web" Controller.
 * Extend from here for application controllers that handle stateful HTTP requests.
 * User sessions-are initialized by default.
 */
abstract class Ilios_Web_Controller extends Ilios_Base_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->load->library('session');
    }


    /**
     * XHR handler.
     * Retrieves instructors and instructor groups, optionally filtered by a given search term.
     * and prints out the search results as JSON-formatted string.
     *
     * Expects the following request query parameters:
     *     'query' ... the search term. if none is provided then all available instructors/instructor-groups are returned.
     *
     * Prints out a JSON formatted object, the actual results array is keyed off by "results"
     * <pre>
     * {
     *   "results": [
     *     {"instructor_group_id": 123, ...}, // instructor groups
     *     ...
     *     {"user_id": 123, ... }, // instructors
     *     ...
     *   ]
     * }
     * </pre>
     * In case an error occurs then an error message will be printed out.
     * <pre>
     * {
     *   "error": "Some error message."
     * }
     * </pre>
     */
    public function searchInstructors ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $rhett['results'] = array();

        $search = ltrim($this->input->get('query'));

        $user = $this->user->getRowForPrimaryKeyId($this->session->userdata('uid'));
        $schoolId =  $user->primary_school_id;

        $instructors = array();
        $query = $this->getFacultyFilteredOnNameMatch($search); // search instructors
        foreach ($query->result_array() as $row) {
            $instructors[] = $this->convertStdObjToArray($row);
        }
        $query->free_result();
        $groups = $this->instructorGroup->getList($schoolId, $search); // search instructor groups
        $rhett['results'] = array_merge($groups, $instructors); // merge groups and instructor

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function getLearnerDashboardSummaryForOffering ()
    {
        $rhett = array();

        // authorization check,
        // must be either a student or one of the instructor/admin-type roles
        if (! $this->session->userdata('is_learner')
            && ! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $offeringId = $this->input->get_post('offering_id');
        $offering = $this->offering->getRowForPrimaryKeyId($offeringId);
        $session = $this->iliosSession->getRowForPrimaryKeyId($offering->session_id);
        $sessionType = $this->sessionType->getRowForPrimaryKeyId($session->session_type_id);
        $course = $this->course->getRowForPrimaryKeyId($session->course_id);
        $userId = $this->session->userdata('uid');

        $tbd = false;
        if ("1" === $session->published_as_tbd || "1" == $course->published_as_tbd) {
            $tbd = true;
        }

        $rhett['is_tbd'] = $tbd;
        $rhett['course'] = $this->convertStdObjToArray($course);
        $rhett['course_objectives'] = $this->course->getObjectivesForCourse($session->course_id, true);
        $rhett['course_learning_materials'] = $this->learningMaterial->getLearningMaterialsForCourse($session->course_id, true);
        $rhett['session'] = $this->convertStdObjToArray($session);
        $rhett['session_type'] = $this->convertStdObjToArray($sessionType);
        $rhett['session_objectives'] = $this->iliosSession->getObjectivesForSession($offering->session_id);
        $rhett['session_learning_materials'] = $this->learningMaterial->getLearningMaterialsForSession($offering->session_id, true);
        $rhett['session_description'] = $this->iliosSession->getDescription($session->session_id);
        $rhett['offering'] = $this->convertStdObjToArray($offering);
        $rhett['offering_instructors'] = $this->offering->getIndividualInstructorsForOffering($offeringId);
        $rhett['is_learner'] = $this->queries->isUserInOfferingAsLearner($userId, $offeringId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function getLearnerDashboardSummaryForSILM ()
    {
        $rhett = array();

        // authorization check,
        // must be either a student or one of the instructor/admin-type roles
        if (! $this->session->userdata('is_learner')
            && ! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $sessionId = $this->input->get_post('session_id');
        $session = $this->iliosSession->getRowForPrimaryKeyId($sessionId);
        $sessionType = $this->sessionType->getRowForPrimaryKeyId($session->session_type_id);
        $course = $this->course->getRowForPrimaryKeyId($session->course_id);
        $silm = $this->iliosSession->getRow('ilm_session_facet', 'ilm_session_facet_id', $session->ilm_session_facet_id);
        $userId = $this->session->userdata('uid');

        $tbd = false;
        if ("1" === $session->published_as_tbd || "1" === $course->published_as_tbd) {
            $tbd = true;
        }

        $rhett['is_tbd'] = $tbd;
        $rhett['course'] = $this->convertStdObjToArray($course);
        $rhett['course_objectives'] = $this->course->getObjectivesForCourse($session->course_id, true);
        $rhett['course_learning_materials'] = $this->learningMaterial->getLearningMaterialsForCourse($session->course_id, true);
        $rhett['session'] = $this->convertStdObjToArray($session);
        $rhett['session_type'] = $this->convertStdObjToArray($sessionType);
        $rhett['session_objectives'] = $this->iliosSession->getObjectivesForSession($sessionId);
        $rhett['session_learning_materials'] = $this->learningMaterial->getLearningMaterialsForSession($sessionId, true);
        $rhett['session_description'] = $this->iliosSession->getDescription($session->session_id);
        $rhett['silm'] = $this->convertStdObjToArray($silm);
        $rhett['is_learner'] = $this->queries->isUserInSILMAsLearner($userId, $session->ilm_session_facet_id);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected parameter:
     *         query
     *
     * @return a JSON'd array of the following:
     *
     * server response to mesh query:
     *         'previous_searches' matches to previous user searches
     *                                 previous user search term
     *                                 mesh object
     *         'search_results'    matches in the mesh universe
     *                                 mesh object
     *
     * mesh object:
     *         'name'            descriptor name
     *         'uid            descriptor uid
     *         'tree_path'        mesh tree
     *         'scope_notes'    array of descriptor's concepts' scope-notes
     *
     * mesh tree:
     *        list of {tree number, name}, {tree number, name}, ... going from root to specific object
     */
    public function searchMeSHUniverseForIlios ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get_post('query');
        $rhett = $this->mesh->searchMeSHUniverseForIlios($matchString);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expects one param 'selection_pairs' which maps to a JSON's array of pairs 'uid'
     *         and 'searchTerm'
     *
     * @return a JSON'd array with a single key of either 'error' or 'success'
     */
    public function saveMeSHSearchSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $pairs = Ilios_Json::deserializeJsonArray($this->input->post('selection_pairs'), true);

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            unset($rhett['error']);

            $this->mesh->startTransaction();

            $this->mesh->saveMeSHSearchSelection($pairs);

            if ($this->mesh->saveMeSHSearchSelection($pairs)
                && (! $this->mesh->transactionAtomFailed())) {
                $this->mesh->commitTransaction();

                $failedTransaction = false;
                $rhett['success'] = 'huzzah';
            } else {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->mesh);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints a JSON-formatted list of program year/cohort data, depending on the current user's permission settings
     * and currently selected active school.
     *
     * @see Cohort::getCohortProgramTreeContent()
     */
    public function getCohortProgramTreeContent ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $activeSchoolId = $this->session->userdata('school_id');
        $userId = $this->session->userdata('uid');
        $rhett = $this->cohort->getCohortProgramTreeContent($activeSchoolId, $userId);
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints a complete course for a given identifier as JSON-formatted text.
     *
     * Expects the following values to be POSTed:
     * - "course_id" ... the course id
     *
     * @see Ilios_Base_Controller::_buildCourseTree()
     */
    public function getDashboardCourseTree ()
    {
        // no authorization check, this info is needs to be available to all logged in users.

        $courseId = $this->input->get_post('course_id');
        $rhett = $this->_buildCourseTree($courseId, true, true);
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints a JSON-formatted list of offerings owned by a given session.
     *
     * Expects the following values to be POSTed:
     * - "session_id" ... the session id
     *
     * @see Offering::_getOfferingsForSession()
     */
    public function getOfferingsForSession ()
    {
        // no authorization check, this info is needs to be available to all logged in users.

        $sessionId = $this->input->get_post('session_id');
        $rhett = $this->offering->getOfferingsForSession($sessionId, "no", "matter", "at all");
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints a JSON-formatted data structure of all competencies and sub-domains owned by the current user's active school.
     *
     * @see Competency::getCompetencyTree()
     */
    public function getCompetencyTree ()
    {
        // no authorization check, this info is needs to be available to all logged in users.
        $schoolId = $this->session->userdata('school_id');
        $rhett = $this->competency->getCompetencyTree($schoolId);
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints a XML-formatted list of enabled users that match a given name/name-fragment and that
     * have been assigned the "Faculty" (aka "Instructor") role.

     * Expects the following values to be POSTed:
     * - "query" ... a name/name-fragment to search users by
     */
    public function getFacultyList ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $query = $this->getFacultyFilteredOnNameMatch($matchString);

        $this->outputQueryResultsAsXML($query);
        $query->free_result();
    }

    /**
     * Prints a XML-formatted list of enabled users that match a given name/name-fragment and that have been assigned
     * the "Course Director" role.
     *
     * Expects the following values to be POSTed:
     * - "query" ... a name/name-fragment to search users by
     */
    public function getDirectorList ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $matchString = $this->input->get('query');
        $query = $this->getDirectorsFilteredOnNameMatch($matchString);

        $this->outputQueryResultsAsXML($query);
        $query->free_result();

    }

    /**
     * Prints out a XML-formatted list of disciplines.
     *
     * Expects the following values to be POSTed:
     * - 'query' ... a title/title-fragment to search disciplines by
     */
    public function getDisciplineList ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $title = $this->input->get('query');
        $schoolId = $this->session->userdata('school_id');
        $query = $this->discipline->getDisciplinesFilteredOnTitleMatch($title, $schoolId);

        $this->outputQueryResultsAsXML($query);
        $query->free_result();
    }

    /**
     * Prints out a JSON-formatted array of courses in a given academic year.
     *
     * Expects the following values to be POSTed:
     * - 'year' ... the academic year
     * - 'sort' ... the sort order
     */
    public function getCourseListForAcademicYear ()
    {
        $academicYear = $this->input->get_post('year');
        $sort = $this->input->get_post('sort');

        $schoolId = $this->session->userdata('school_id');
        $results = $this->course->getCoursesForAcademicYear($academicYear, $schoolId);

        if (!empty($sort)) {
            $sortkeys = array();
            foreach ($results as $key => $data) {
                $sortkeys[$key] = $data[$sort];
            }
            array_multisort($sortkeys, $results);
        }

        header("Content-Type: text/plain");
        echo json_encode($results);
    }


    /**
     * @todo add code docs
     */
    protected function _getUserPreferences ()
    {
        $rhett = array();

        if (! $this->session->userdata('username')) {
            $rhett['py_archiving'] = 'false';
            $rhett['course_archiving'] = 'false';
            $rhett['course_rollover'] = 'false';
        } else {
            // $userId setting left for future developers should they want to have prefs stored in
            //          the db keyed by user_id
            // $userId = $this->session->userdata('uid');

            $rhett['py_archiving'] = $this->session->userdata('py_archiving') ? 'true' : 'false';
            $rhett['course_archiving'] = $this->session->userdata('course_archiving') ? 'true'
                : 'false';
            $rhett['course_rollover'] = $this->session->userdata('course_rollover') ? 'true'
                : 'false';
        }
        return $rhett;
    }
}
