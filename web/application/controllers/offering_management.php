<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Offering management controller
 */
class Offering_Management extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Alert', 'alert', TRUE);
        $this->load->model('Group', 'group', TRUE);
        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
        $this->load->model('Program', 'program', TRUE);
        $this->load->model('School', 'school', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Required POST or GET parameters:
     *      session_id          (session id)
     */
    public function index ()
    {
        $data = array();

        if (!$this->session->userdata('has_instructor_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $this->output->set_header('Expires: 0');


        $data['session_id'] = (int) $this->input->get_post('session_id');

        $data['viewbar_title'] = $this->config->item('ilios_institution_name');

        $schoolId =  $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if ($schoolRow != null) {
            $data['school_id'] = $schoolId;
            $data['school_name'] = $schoolRow->title;
            if ($schoolRow->title != null) {
                $key = 'general.phrases.school_of';
                $schoolOfStr = $this->languagemap->getI18NString($key);
                $data['viewbar_title'] .= ' ' . $schoolOfStr . ' ' . $schoolRow->title;
            }
        } else {
            // not sure how to proceed if user is not tied to a particular school.
            // for now, we just proceed.
        }

        // get school competencies
        $schoolCompetencies = $this->_getSchoolCompetencies();
        $data['school_competencies'] = Ilios_Json::encodeForJavascriptEmbedding($schoolCompetencies,
            Ilios_Json::JSON_ENC_SINGLE_QUOTES);

        $key = 'offering_management.calendar.lightbox.recurs_on_days';
        $data['repeat_weekday_selector_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.lightbox.recurs_count';
        $data['repeat_ends_on_count_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.lightbox.recurs_date';
        $data['repeat_ends_on_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.lightbox.select_groups';
        $data['select_groups_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.lightbox.select_instructors';
        $data['select_instructors_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.show_busy.cohorts';
        $data['show_busy_cohorts_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.show_busy.instructors';
        $data['show_busy_instructors_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.show_busy.students';
        $data['show_busy_students_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.show_entire_events';
        $data['show_all_events_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.calendar.show_session_events';
        $data['show_session_events_string'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.learner_view';
        $data['see_learner_view'] = $this->languagemap->getI18NString($key);

        $key = 'offering_management.title_bar';
        $data['title_bar_string'] = $this->languagemap->getI18NString($key);
        //$data['viewbar_title'] = $data['title_bar_string'];

        $key = 'general.phrases.end_time';
        $data['phrase_end_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.not_recurring';
        $data['phrase_not_recurring_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.start_time';
        $data['phrase_start_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.student_group';
        $data['phrase_student_group_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.student_groups';
        $data['phrase_student_groups_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.time_range';
        $data['phrase_time_range_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.course';
        $data['word_course_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.date';
        $data['word_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.filter';
        $data['word_filter_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.instructors';
        $data['word_instructors_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.instructor_indefinite';
        $data['word_instructors_indefinite_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.room';
        $data['word_room_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.session';
        $data['word_session_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.status';
        $data['word_status_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.weeks';
        $data['word_weeks_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.calendar.sunday_short';
        $data['calendary_short_sunday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.monday_short';
        $data['calendary_short_monday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.tuesday_short';
        $data['calendary_short_tuesday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.wednesday_short';
        $data['calendary_short_wednesday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.thursday_short';
        $data['calendary_short_thursday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.friday_short';
        $data['calendary_short_friday_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.calendar.saturday_short';
        $data['calendary_short_saturday_string'] = $this->languagemap->getI18NString($key);

        $titles = $this->course->getSessionAndCourseMetaForSession($data['session_id']);
        if (count($titles) == 0) {
            // error state TODO
        }
        else {
            $data['course_title'] = $titles['course_title'];
            $data['course_id'] = $titles['course_id'];
            // note that this produces incorrect string ranges for years < 2000 and > 2099
            $data['course_year_string'] = $titles['course_year'] . '-'
                                                . ($titles['course_year'] - 1999);
            $data['course_start_date'] = $titles['course_start_date'];
        }

        $data['calendar_start_date'] = $this->input->get_post('start_date', true);
        if (false === $data['calendar_start_date']) {
            $data['calendar_start_date'] = $data['course_start_date'];
        }

        $data['session_model'] = $this->iliosSession->getRowForPrimaryKeyId($data['session_id']);
        $data['mesh_terms']
                           = $this->iliosSession->getMeSHDescriptorsForSession($data['session_id']);
        $data['objectives'] = $this->iliosSession->getObjectiveTextsForSession($data['session_id']);
        $data['learning_materials']
                     = $this->learningMaterial->getLearningMaterialsForSession($data['session_id']);


        $data['session_type_array'] = $this->sessionType->getList($schoolId);


        $cohorts = $this->getCohortsForSessionId($data['session_id']);


        $schoolIds = $this->getSchoolIdsForCohorts($cohorts);

        $data['student_groups'] = $this->getStudentGroupTreesForCohorts($cohorts);

        $this->load->view('offering/offering_manager', $data);
    }

    /**
     * Adds or updates a session offering, based on posted user input.
     * Expected parameters:
     *      . sid           session id
     *      . start_date
     *      . end_date
     *      . location
     *      . instructors
     *      . student_group_ids
     *      . offering_id
     *      . calendar_id
     *      . parent_publish_event_id
     *      . is_recurring
     *      . recurring_event   cannot be empty is is_recurring is true
     * @return a JSON'd array with key 'calendar_id' and either 'error', or 'offering_id' and
     *                          potentially 'recurring_event_id' if the offering has a recurring
     *                          event - with 'recurring_event_was_added' being true or false
     *                          depending on whether it already existed in the db before the save
     *                          (this information is used by the client on return to know whether
     *                          it need instantiate new offerings or whether they already exist)
     */
    public function saveOffering ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $sessionId = $this->input->get_post('sid');
        $sessionIsPublished = $this->iliosSession->isPublished($sessionId);

        $school = $this->school->getSchoolBySessionId($sessionId);
        // check if session is linked to a school
        // if this is not the case then echo out an error message
        // and be done with it.
        if (empty($school)) {
            $msg = $this->languagemap->getI18NString('offering_management.error.failed_save');
            $rhett = array();
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $userId = $this->session->userdata('uid');

        $calendarId = $this->input->get_post('calendar_id');

        $startDate = $this->input->get_post('start_date');
        $endDate = $this->input->get_post('end_date');

        $location = $this->input->get_post('location');

        $instructorArray = json_decode(rawurldecode($this->input->get_post('instructors')), true);
        $studentGroupIds = explode(',', $this->input->get_post('student_group_ids'));

        $offeringId = $this->input->get_post('offering_id');
        if (($offeringId == '') || ($offeringId == -1)) {
            $offeringId = null;
        }

        $recurringEvent = null;
        $recurringEventAdded = false;
        if ($this->input->get_post('is_recurring') == 'true') {
            $recurringEvent = json_decode(rawurldecode($this->input->get_post('recurring_event')),
                                          true);

            $recurringEventAdded = ($recurringEvent['dbId'] == -1);
        }

        $publishEventId = $this->input->get_post('parent_publish_event_id');

        $rhett['calendar_id'] = $calendarId;


        $previousInstructors = null;
        $previousLearners = null;
        $previousOfferingRow = null;
        $isNewOffering = is_null($offeringId);

        if (! $isNewOffering) {
            $previousInstructors = $this->offering->getInstructorsForOffering($offeringId);
            $previousInstructors = array_merge($previousInstructors, $this->offering->getInstructorGroupsForOffering($offeringId));
            $previousLearners = $this->offering->getLearnersAndLearnerGroupsForOffering($offeringId);
            $previousOfferingRow = $this->offering->getRowForPrimaryKeyId($offeringId);
        }

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();
            unset($rhett['error']);

            $this->offering->startTransaction();

            $alertChangeTypes = array();

            if ($offeringId == null) {
                $alertChangeTypes[] = Alert::CHANGE_TYPE_NEW_OFFERING;
            }

            $results = $this->offering->saveOffering($location, $startDate, $endDate,
                                                     $instructorArray, $studentGroupIds,
                                                     $sessionId, $recurringEvent,
                                                     $publishEventId, $auditAtoms, $offeringId);
            $offeringId = $results['offering_id'];

            if (($offeringId == -1) || $this->offering->transactionAtomFailed()) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');
                $rhett['error'] = $msg;
            }  else {
                $rhett['offering_id'] = $offeringId;
                $rhett['location'] = $results['location'];
                if (isset($results['recurring_event_id'])) {
                    $rhett['recurring_event_id'] = $results['recurring_event_id'];
                    $rhett['recurring_event_was_added'] = $recurringEventAdded ? 'true' : 'false';
                }

                $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($sessionId);

                if ($sessionIsPublished) {
                    if (! $isNewOffering) {
                        $currentInstructors = $this->offering->getInstructorsForOffering($offeringId);
                        $currentInstructors = array_merge($currentInstructors,
                            $this->offering->getInstructorGroupsForOffering($offeringId));
                        // get the ids of all instructors/instructor-groups that were added and removed
                        // to/from this offering with the last save
                        $instructorsDiff = array_merge(
                                array_udiff($previousInstructors, $currentInstructors,
                                    array('Offering_Management', 'instructorComparator')),
                                array_udiff($currentInstructors, $previousInstructors,
                                    array('Offering_Management', 'instructorComparator'))
                        );
                        if (count($instructorsDiff)) {
                            $alertChangeTypes[] = Alert::CHANGE_TYPE_INSTRUCTOR;
                        }


                        $currentLearners = $this->offering->getLearnersAndLearnerGroupsForOffering($offeringId);
                        // get the ids of all learners/learner-groups that were added and removed
                        // to/from this offering with the last save
                        $learnersDiff = array_merge(
                                array_udiff($previousLearners, $currentLearners,
                                        array('Offering_Management', 'learnerComparator')),
                                array_udiff($currentLearners, $previousLearners,
                                        array('Offering_Management', 'learnerComparator'))
                        );
                        if (count($learnersDiff)) {
                            $alertChangeTypes[] = Alert::CHANGE_TYPE_LEARNER_GROUP;
                        }

                        if ($location != $previousOfferingRow->room) {
                            $alertChangeTypes[] = Alert::CHANGE_TYPE_LOCATION;
                        }

                        if (($startDate != $previousOfferingRow->start_date) || ($endDate != $previousOfferingRow->end_date)) {
                            $alertChangeTypes[] =  Alert::CHANGE_TYPE_TIME;
                        }
                    }

                    // Add or Update alert only if there is one or more identified change type.
                    if (count($alertChangeTypes)) {
                        $msg = $this->alert->addOrUpdateAlert($offeringId, 'offering', $userId, $school, $alertChangeTypes);
                        if (! is_null($msg)) {
                            $rhett['error'] = $msg;
                        }
                        if ($this->offering->transactionAtomFailed()) {
                            $msg = $this->languagemap->getI18NString('general.error.db_insert');
                            $rhett['error'] = $msg;
                        }
                    }
                }
            }

            if (isset($rhett['error'])) {
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->offering);
            } else {
                $failedTransaction = false;
                $this->offering->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Callback function for <code>array_udiff()</code>.
     * Compares two given arrays representing instructors or instructor groups.
     * @param array $a instructor or instructor-group "A"
     * @param array $b instructor or instructor-group "B"
     * @return int
     * @see Offering_Management::saveOffering()
     * @link http://php.net/manual/en/function.array-udiff.php
     */
    public static function instructorComparator ($a, $b)
    {
        if (isset($a['user_id'])) { // compare user ids
            if (isset($b['user_id'])) {
                return $a['user_id'] - $b['user_id'];
            }
            return 1;
        } else { // compare group ids
            if (isset($b['instructor_group_id'])) {
                return $a['instructor_group_id'] - $b['instructor_group_id'];
            }
            return -1;
        }
    }

    /**
     * Callback function for <code>array_udiff()</code>.
     * Compares two given arrays representing learners or learner groups.
     * @param array $a learner or learner-group "A"
     * @param array $b learner or learner-group "B"
     * @return int
     * @see Offering_Management::saveOffering()
     * @link http://php.net/manual/en/function.array-udiff.php
     */
    public static function learnerComparator ($a, $b)
    {
        if (isset($a['user_id'])) {
            if (isset($b['user_id'])) {
                return $a['user_id'] - $b['user_id'];
            }

            return 1;
        }
        else {
            if (isset($b['group_id'])) {
                return $a['group_id'] - $b['group_id'];
            }

            return -1;
        }
    }

    /**
     * Expected parameters:
     *      . oid           offering id
     *      . calendar_id
     *
     * @return a JSON'd array with the key 'calendar_id' and the key 'error' if an error occurred
     */
    public function deleteOffering ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $offeringId = $this->input->get_post('oid');

        $calendarId = $this->input->get_post('calendar_id');

        $rhett['calendar_id'] = $calendarId;

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->offering->startTransaction();

            if (! $this->offering->deleteOffering($offeringId, $auditAtoms, true)
                                                    || $this->offering->transactionAtomFailed()) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');

                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->offering);
            }
            else {
                $this->offering->commitTransaction();

                $failedTransaction = false;

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected parameters:
     *      . course_id
     *
     * @return a JSON'd array with the key 'error' if an error occurred, else an array of offering
     *              models; the models include a key for session_type_id to faciliate calendar
     *              rendering requirements.
     */
    public function loadOfferingsForCourse ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $offeringArray = array();

        $courseId = $this->input->get_post('course_id');
        $sessions = $this->iliosSession->getSimplifiedSessionsForCourse($courseId);
        foreach ($sessions as $session) {
            $offerings = $this->offering->getOfferingsForSession($session['session_id'],
                                                                 $session['title'],
                                                                 $session['session_type_id'],
                                                                 ($session['published_as_tbd']
                                                                                           == '1'));

            foreach ($offerings as $offering) {
                array_push($offeringArray, $offering);
            }

            if (isset($session['ilm_session_facet_id'])
                    && (! is_null($session['ilm_session_facet_id']))) {
                $silm = $this->iliosSession->getSILM($session['ilm_session_facet_id']);

                $silm['session_id'] = $session['session_id'];
                $silm['session_title'] = $session['title'];
                $silm['is_tbd'] = ($session['published_as_tbd'] ? 'true' : 'false');
                $silm['publish_event_id'] = $session['publish_event_id'];

                array_push($offeringArray, $silm);
            }
        }

        // todo error cases
        $rhett['offerings'] = $offeringArray;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected parameters:
     *      . session_id
     *
     * @return a JSON'd array with the key 'error' if an error occurred, else an array of instructor
     *              models keyed as 'instructors' (either instructor group, or user) with an extra
     *              key ('offerings') listing the offerings
     */
    public function getOfferingsForAllInstructorsInSession ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $sessionId = $this->input->get_post('session_id');

        // @todo running queries per line item is inefficient, build a better solution to this. [ST 2013/11/06]
        $instructors = $this->iliosSession->getInstructorsForSession($sessionId);
        for ($i = 0, $n = count($instructors); $i < $n; $i++) {
            $instructors[$i]['offerings'] =
                $this->offering->getOtherOfferingsForInstructor($sessionId, $instructors[$i]['user_id']);
        }
        $instructorGroups = $this->iliosSession->getInstructorGroupsForSession($sessionId);
        for ($i = 0, $n = count($instructorGroups); $i < $n; $i++) {
            $instructorGroups[$i]['offerings'] =
                $this->offering->getOtherOfferingsForInstructorGroup($sessionId, $instructorGroups[$i]['instructor_group_id']);
        }
        $rhett['instructors'] = array_merge($instructors, $instructorGroups);

        // @todo error case

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected parameters:
     *      . session_id
     *
     * @return a JSON'd array with the key 'error' if an error occurred, else an array of student
     *              groups models keyed as 'learners' with an extra key ('offerings') listing
     *              the offerings
     */
    public function getOfferingsForAllLearnersInSession ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $sessionId = $this->input->get_post('session_id');

        // @todo replace inefficient code that queries in loop. [ST 2013/11/13]
        $learners =  $this->iliosSession->getLearnersForSession($sessionId);
        for ($i = 0, $n = count($learners); $i < $n; $i++) {
            $learners[$i]['offerings'] =
                $this->offering->getOtherOfferingsForInstructor($sessionId, $learners[$i]['user_id']);
        }
        $learnerGroups = $this->iliosSession->getLearnerGroupsForSession($sessionId);
        for ($i = 0, $n = count($learnerGroups); $i < $n; $i++) {
            $learnerGroups[$i]['offerings'] =
                $this->offering->getOtherOfferingsForInstructorGroup($sessionId, $learnerGroups[$i]['group_id']);
        }
        $rhett['learners'] = array_merge($learners, $learnerGroups);

        // @todo error case

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * For out-of-session offerings (for instance i'm editing offerings for session A but have
     *      chosen to see all offerings for the course, and so am wanting to see information about
     *      an offering in session B), the inspector pane needs certain extra information concerning
     *      a session. This returns that.
     *
     * Expected params:
     *      . session_id
     *
     * @return a JSON'd array with either one key ('error'), or the following keys:
     *                  . special_equipment (0 or 1)
     *                  . attire (0 or 1)
     *                  . supplemental (0 or 1)
     *                  . objectives (non-associative array of strings)
     *          once we figure stuff out more, this will return vocabulary and learning-material
     *              related stuff
     */
    public function getExtraInspectorContentsForSession ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        // todo error cases
        $sessionId = $this->input->get_post('session_id');

        $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($sessionId);
        $rhett['special_equipment'] = $sessionRow->equipment_required;
        $rhett['attire'] = $sessionRow->attire_required;
        $rhett['supplemental'] = $sessionRow->supplemental;

        $rhett['objectives'] = $this->iliosSession->getObjectiveTextsForSession($sessionId);
        $rhett['mesh_terms'] = $this->iliosSession->getMeSHDescriptorsForSession($sessionId);
        $rhett['learning_materials']
                            = $this->learningMaterial->getLearningMaterialsForSession($sessionId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Cohorts are returned as stdObj and not arrays. (session -> course ->* cohorts)
     */
    protected function getCohortsForSessionId ($sessionId)
    {
        $row = $this->iliosSession->getRowForPrimaryKeyId($sessionId);
        $courseId = $row->course_id;
        return $this->course->getCohortsForCourse($courseId);
    }

    //
    //
    // TODO getStudentGroupTreesForCohorts and addGroupModelForProgram are in both CM and OM
    // controllers.. the methods have model dependencies i'm not prepared to introduce into
    // the abstract superclass, nor am i crazy about having methods in the superclass which
    // require subclasses using them to specify the dependencies out of class.. seems
    // funky. I suppose we could make another abstract class intermediary between
    // AIC and CM/OM
    //
    //

    /**
    * Returns a non-associative array of student group tree models which are associated to the
    *  cohorts (cohorts -> groups). $cohorts is assumed to be coming from the return of
    *  getCohortsForSessionId($sessionId)
    */
    protected function getStudentGroupTreesForCohorts ($cohorts)
    {
        $rhett = array();

        foreach ($cohorts as $cohort) {
            $groupIds = $this->cohort->getGroupIdsForCohortWithId($cohort->cohort_id);

            foreach ($groupIds as $groupId) {
                $groupModel = $this->group->getModelArrayForGroupId($groupId);
                $groupModel['subgroups'] = $this->group->getSubgroupsForGroupId($groupId);

                $programYearRow = $this->programYear->getRowForPrimaryKeyId($cohort->program_year_id);
                $programRow = $this->program->getRowForPrimaryKeyId($programYearRow->program_id);

                $this->addGroupModelForProgram($groupModel, $programRow->program_id,
                $programRow->title, $cohort->program_year_id,
                $cohort->title, $rhett);
            }
        }
        return $rhett;
    }

    private function addGroupModelForProgram ($groupModel, $programId, $programTitle, $programYearId, $cohortTitle, &$array)
    {
        $programYearModel = isset($array[$programYearId]) ? $array[$programYearId] : null;

        if ($programYearModel == null) {
            $programYearModel = array();

            $programYearModel['program_id'] = $programId;
            $programYearModel['program_year_id'] = $programYearId;
            $programYearModel['title'] = $programTitle . ' - ' . $cohortTitle;
            $programYearModel['groups'] = array();
        }

        array_push($programYearModel['groups'], $groupModel);
        $array[$programYearId] = $programYearModel;
    }
}
