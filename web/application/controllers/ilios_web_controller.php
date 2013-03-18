<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $rhett['results'] = array();

        $search = ltrim($this->input->get('query'));

        $user = $this->user->getRowForPrimaryKeyId($this->session->userdata('uid'));
        $schoolId =  $user->primary_school_id;

        $instructors = array();
        $queryResult = $this->getFacultyFilteredOnNameMatch($search); // search instructors
        foreach ($queryResult->result_array() as $row) {
            $instructors[] = $this->convertStdObjToArray($row);
        }
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
        $lang =  $this->getLangToUse();

        // authorization check,
        // must be either a student or one of the instructor/admin-type roles
        if (! $this->session->userdata('is_learner')
            && ! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $offeringId = $this->input->get_post('offering_id');
        $offering = $this->offering->getRowForPrimaryKeyId($offeringId);
        $session = $this->iliosSession->getRowForPrimaryKeyId($offering->session_id);
        $sessionType = $this->sessionType->getRowForPrimaryKeyId($session->session_type_id);
        $course = $this->course->getRowForPrimaryKeyId($session->course_id);
        $userId = $this->session->userdata('uid');

        $rhett['course'] = $this->convertStdObjToArray($course);
        $rhett['course_objectives'] = $this->course->getObjectivesForCourse($session->course_id, true);
        $rhett['course_learning_materials'] = $this->learningMaterial->getLearningMaterialsForCourse($session->course_id, true);
        $rhett['is_tbd'] = ($session->published_as_tbd == '1');
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
        $lang =  $this->getLangToUse();

        // authorization check,
        // must be either a student or one of the instructor/admin-type roles
        if (! $this->session->userdata('is_learner')
            && ! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $sessionId = $this->input->get_post('session_id');
        $session = $this->iliosSession->getRowForPrimaryKeyId($sessionId);
        $sessionType = $this->sessionType->getRowForPrimaryKeyId($session->session_type_id);
        $course = $this->course->getRowForPrimaryKeyId($session->course_id);
        $silm = $this->iliosSession->getRow('ilm_session_facet', 'ilm_session_facet_id', $session->ilm_session_facet_id);
        $userId = $this->session->userdata('uid');

        $rhett['course'] = $this->convertStdObjToArray($course);
        $rhett['course_objectives'] = $this->course->getObjectivesForCourse($session->course_id, true);
        $rhett['course_learning_materials'] = $this->learningMaterial->getLearningMaterialsForCourse($session->course_id, true);
        $rhett['is_tbd'] = ($session->published_as_tbd == '1');
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
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $pairs = json_decode(urldecode($this->input->get_post('selection_pairs')), true);

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
                $rhett['error'] = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);
                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->mesh);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @return a JSON'd array which features the cohort program tree
     */
    public function getCohortProgramTreeContent ()
    {
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $activeSchoolId = $this->session->userdata('school_id');
        $userId = $this->session->userdata('uid');
        $rhett = $this->cohort->getCohortProgramTreeContent($activeSchoolId, $userId);
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Retrieves and prints a complete course for a given identifier as JSON-formatted text.
     * Expects the following values to be POSTed:
     * - "course_id" ... the course identifier [integer]
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
     * Expected params:
     *    session_id
     *
     * @return a JSON'd array with key 'error', or the key 'container' - a passback of the
     *                         cnumber param value - and the key 'added' - the number of offerings
     *                         added
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
     * This takes no arguments presently and returns a tree of all competencies and sub-domains
     * owned by the curent user's selected school.
     *
     * @return this returns a JSON'd non-associative array of competency objects, each object being
     *                 an associative array with keys 'competency_id', 'title', and 'subdomains'. the
     *                 value for the 'subdomains' key is a non-associative array of competency objects
     *                 without further subdomains
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
     * XHR handler.
     * Retrieves and prints a list of enabled users that match a given name/name-fragment and that
     * have been assigned the "Faculty" (aka "Instructor") role.
     * Expects the following values to be POSTed:
     * - "query" ... a name/name-fragment to search users by
     */
    public function getFacultyList ()
    {
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $matchString = $this->input->get('query');
        $queryResults = $this->getFacultyFilteredOnNameMatch($matchString);

        $this->outputQueryResultsAsXML($queryResults);
    }

    /**
     * XHR handler.
     * Retrieves and prints a list of enabled users that match a given name/name-fragment and that
     * have been assigned the "Course Director" role.
     * Expects the following values to be POSTed:
     * - "query" ... a name/name-fragment to search users by
     */
    public function getDirectorList ()
    {
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $matchString = $this->input->get('query');
        $queryResults = $this->getDirectorsFilteredOnNameMatch($matchString);

        $this->outputQueryResultsAsXML($queryResults);

    }

    /**
     * XHR Handler.
     * Prints out a JSON-formatted array of disciplines.
     * Expects the following values to be POSTed:
     * - 'query' ... a title/title-fragment to search disciplines by
     */
    public function getDisciplineList ()
    {
        $lang =  $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $title = $this->input->get('query');
        $schoolId = $this->session->userdata('school_id');
        $queryResults = $this->discipline->getDisciplinesFilteredOnTitleMatch($title, $schoolId);

        $this->outputQueryResultsAsXML($queryResults);
    }

    /**
     * XHR Handler.
     * Prints out a JSON-formatted array of courses in a given academic year.
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
}