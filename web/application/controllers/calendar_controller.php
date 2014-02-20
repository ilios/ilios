<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * This is the user calendar controller.
 */
class Calendar_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();

        $this->load->model('Instructor_Group', 'instructorGroup', TRUE);
        $this->load->model('Program', 'program', TRUE);
        $this->load->model('Report', 'report', TRUE);
        $this->load->model('School', 'school', TRUE);
        $this->load->model('User', 'user', TRUE);
        $this->load->model('User_Made_Reminder', 'reminder', TRUE);
        $this->load->model('Authentication', 'authentication', TRUE);

        $this->load->library("ICalExporter");
        $this->load->library('CalendarFeedDataProvider');
    }

    /**
     * @todo add code docs
     */
    public function index ()
    {
        $data = array();

        // authorization check
        $isStudent = $this->session->userdata('is_learner');
        $hasInstructorAccess = $this->session->userdata('has_instructor_access');

        if (! $isStudent && ! $hasInstructorAccess) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $this->output->set_header('Expires: 0');

        $change_school = $this->input->get_post('schoolselect');
        if ($change_school) {
            $this->_setActiveSchool($change_school);
        }

        $schoolId = $this->session->userdata('school_id');

        if (! in_array($schoolId, $this->_getAvailableSchools())) {
            // Reset to primary school
            $this->_setActiveSchool($this->session->userdata('primary_school_id'));
            $schoolId = $this->session->userdata('school_id');
        }

        $schoolTitle = null;

        if ($schoolId) {
            $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);
            if ($schoolRow) {
                $schoolTitle = $schoolRow->title;
            }
        }

        $data['viewbar_title'] = $this->config->item('ilios_institution_name');

        if ($schoolTitle != null) {
            $key = 'general.phrases.school_of';
            $schoolOfStr = $this->languagemap->getI18NString($key);
            $data['viewbar_title'] .= ' ' . $schoolOfStr . ' ' . $schoolTitle;

            $availSchools = $this->_getAvailableSchools();

            if (count($availSchools) > 1) {
                $school_ids = $availSchools;
                $schools = array();
                foreach ($school_ids as $sid) {
                    $row = $this->school->getRowForPrimaryKeyId($sid);
                    $schools[$sid] = $row->title;
                }
                $data['available_schools'] = $schools;
                $data['selected_school_id'] = $schoolId;

                $key = 'general.phrases.select_school';
                $data['select_school_string'] = $this->languagemap->getI18NString($key);
            }
        }

        // get school competencies
        $schoolCompetencies = $this->_getSchoolCompetencies();
        $data['school_competencies'] = Ilios_Json::encodeForJavascriptEmbedding($schoolCompetencies,
            Ilios_Json::JSON_ENC_SINGLE_QUOTES);

        $key = 'dashboard.account_mgmt';
        $data['account_management_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.competency';
        $data['competency_mapping_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.my_alerts';
        $data['my_alerts_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.my_calendar';
        $data['my_calendar_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.my_courses';
        $data['my_courses_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.my_programs';
        $data['my_programs_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.my_reports';
        $data['my_reports_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.recent_activities';
        $data['recent_activities_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.title';
        $data['title_bar_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.view_public';
        $data['view_public_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.administration.course_rollover';
        $data['course_rollover_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.administration.management_console';
        $data['management_console_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.reminder.mark_complete';
        $data['mark_complete_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.reminder.max_chars';
        $data['max_char_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'dashboard.reminder.your_alert';
        $data['your_alert_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.report.association';
        $data['report_association_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.report.header';
        $data['report_header_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.report.title';
        $data['report_title_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.icalendar.download_title';
        $data['ical_download_title'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.add_new';
        $data['phrase_add_new_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.advanced_search';
        $data['phrase_advanced_search_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.due_date';
        $data['phrase_due_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.end_time';
        $data['phrase_end_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.search_ilios';
        $data['phrase_search_ilios_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.start_time';
        $data['phrase_start_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.student_group';
        $data['phrase_student_group_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.time_range';
        $data['phrase_time_range_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.welcome_back';
        $data['phrase_welcome_back_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.which_is';
        $data['phrase_which_is_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.terms.all';
        $data['word_all_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.administration';
        $data['word_administration_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.archiving';
        $data['word_archiving_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.cancel';
        $data['word_cancel_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.course';
        $data['word_course_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.created';
        $data['word_created_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.terms.date';
        $data['word_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.done';
        $data['word_done_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.event';
        $data['word_event_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.filter';
        $data['word_filter_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.help';
        $data['word_help_string'] = $this->languagemap->getI18NString($key);

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

        $key = 'learning_material.dialog.title';
        $data['learning_materials_dialog_title'] = $this->languagemap->getI18NString($key);

        $key = 'mesh.dialog.search_mesh';
        $data['mesh_search_mesh']= $this->languagemap->getI18NString($key);

        $key = 'mesh.dialog.title';
        $data['mesh_dialog_title']= $this->languagemap->getI18NString($key);

        $data['render_headerless'] = false;
        $data['show_view_switch'] = false;

        $key = 'calendar.ical';
        $data['ical_download_button'] = $this->languagemap->getI18NString($key);

        $key = 'calendar.filters_set_filters';
        $data['calendar_filters_btn'] = $this->languagemap->getI18NString($key);

        $key = 'calendar.filters_title';
        $data['calendar_filters_title'] = $this->languagemap->getI18NString($key);

        $key = 'calendar.filters_clear_search';
        $data['calendar_clear_search_filters'] = $this->languagemap->getI18NString($key);

        $key = 'calendar.filters_search_mode_title';
        $data['calendar_search_mode_title'] = $this->languagemap->getI18NString($key);

        $fdata = array();   // Data for calendar filter's content
        $fdata['calendar_filters_title'] = $data['calendar_filters_title'];
        $fdata['search_by_course_text'] = $this->languagemap->getI18NString('calendar.filters_search_by_course_text');
        $fdata['search_by_topic_text'] = $this->languagemap->getI18NString('calendar.filters_search_by_topic_text');
        $fdata['academic_year_title'] = $this->languagemap->getI18NString('calendar.filters_academic_year_title');

        $fdata['discipline_titles'] = $this->discipline->getAllDisciplineTitles($schoolId);
        $fdata['session_type_titles'] = $this->sessionType->getSessionTypeTitles($schoolId);
        // Currently course levels are hard coded in course_container_include.php
        $level = $this->languagemap->getI18NString('general.terms.level');
        $fdata['course_levels'] = array( 1 => "$level I",
                                         2 => "$level II",
                                         3 => "$level III",
                                         4 => "$level IV",
                                         5 => "$level V");

        $programcohorts = $this->programYear->getAllProgramCohortsWithSchoolId($schoolId);
        if (!empty($programcohorts)) {
            $fdata['program_cohort_titles'] = array_combine(array_keys($programcohorts),
                                                            array_map(create_function( '$n', 'return $n["program_cohort_title"];'), $programcohorts));
            asort($fdata['program_cohort_titles']);
        } else {
            $fdata['program_cohort_titles'] = $programcohorts;
        }

        $fdata['course_titles'] = $this->course->getAllCourseTitles($schoolId);
        if (!empty($fdata['course_titles'])) {
            asort($fdata['course_titles']);
        }

        $data['calendar_filters_data'] = $fdata;

        // render calendar view, based on user's roles and/or preferences.
        if ($isStudent && $hasInstructorAccess) { // user is both student and has instructor-level access
            // set the dashboard view based on the preferences set in the user session.
            // by default, show the "student view" of the calendar.
            $dashboardView = $this->session->userdata('calendar_view');
            $data['show_view_switch'] = true;

            switch ($dashboardView) {
                case 'instructor' :
                    $key = 'calendar.switch_to_student_view';
                    $data['switch_to_student_view_string']= $this->languagemap->getI18NString($key);
                    $this->_viewInstructorCalendar($data);
                    break;
                case 'student' :
                default :
                    $key = 'calendar.switch_to_instructor_view';
                    $data['switch_to_instructor_view_string']= $this->languagemap->getI18NString($key);
                    $this->_viewStudentCalendar($data);
            }
            return;
        } elseif ($isStudent) { // user is a learner only
            $this->_viewStudentCalendar($data);
            return;
        } else { // user has instructor-level access only
            $this->_viewInstructorCalendar($data);
        }
    }

    /**
     * Controller action.
     *
     * Sets the preferred view on request
     * and stores this preference in the current user session.
     *
     * Expected request parameters:
     *    "preferred_view" ... either "student or "instructor",
     *        depending on what view the users wants to switch to
     */
    public function switchView ()
    {
        $role = $this->input->get('preferred_view', false);
        $this->_setViewPreferenceByRole('calendar_view', $role);
        redirect('/calendar_controller'); // redirect to itself
    }


    /**
     * Get all offerings (except Session Independent Learnings) where the user is in a student group
     *      which has been associated to an offering
     *
     * @return a JSON'd array with key 'error', or the key 'offerings' with an array of objects -
     *              each object containing the keys offering_id, start_date, end_date,
     *                                              session_id, course_title, course_id,
     *                                              session_title, session_type_css_class
     */
    public function getOfferingsForLearnerDashboard ()
    {
        // authorization check
        if (! $this->session->userdata('is_learner')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $userId = $this->session->userdata('uid');
        $userRoles = array(User_Role::STUDENT_ROLE_ID);
        $year = null;
        $includeArchived = false;

        // Retrieve filters params
        $filters = $this->input->get_post('filters');
        if (!empty($filters)) {
            $filters = json_decode($filters);
            if ($filters->showAllActivities) {
                $userId = null;
            }
            $year = $filters->academicYear;
            $includeArchived = true;
        }

        $rhett = array();
        $rhett['offerings'] = array();

        $visualAlertThreshold = $this->config->item('visual_alert_threshold_in_days');
        if (empty($visualAlertThreshold)) { // apply default
            $visualAlertThreshold = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS;
        }

        $offerings = $this->queries->getOfferingsForCalendar($schoolId, $userId,
                $userRoles, $year, $includeArchived, $visualAlertThreshold);

        $rhett['offerings'] = $this->_applyFiltersOnOfferings($filters, $offerings);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Get all Session Independent Learnings where the user is in a student group
     *      which has been associated to a SILM
     *
     * @return a JSON'd array with key 'error', or the key 'silms' with an array of objects -
     *              each object containing the keys session_id, due_date, course_title, course_id,
     *                                              session_title, session_type_css_class
     */
    public function getSessionILMsForLearnerDashboard () {

        // authorization check
        if (! $this->session->userdata('is_learner')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $userId = $this->session->userdata('uid');
        $userRoles = $this->userRole->getRoleId('student');    // Student
        $year = null;
        $includeArchived = false;

        // Retrieve filters params
        $filters = $this->input->get_post('filters');
        if (!empty($filters)) {
            $filters = json_decode($filters);
            if ($filters->showAllActivities) {
                $userId = null;
            }
            $year = $filters->academicYear;
            $includeArchived = true;
        }

        $rhett = array();

        $visualAlertThreshold = $this->config->item('visual_alert_threshold_in_days');
        if (empty($visualAlertThreshold)) { // apply default
            $visualAlertThreshold = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS;
        }

        //$silms = $this->queries->getSILMsForUserAsLearner($userId);
        $silms = $this->queries->getSILMsForCalendar($schoolId, $userId, $userRoles,
                $year, $includeArchived, $visualAlertThreshold);

        $rhett['silms'] = $this->_applyFiltersOnOfferings($filters, $silms);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Get all offerings (except Session Independent Learnings) where the user is any of:
     *      . an instructor for an offering
     *      . a course director of a course (so all offerings for all sessions of this course)
     *      . a program year director (so all offerings for all sessions for all courses which have
     *                                      that program year's cohort associated to them)
     *
     * @return a JSON'd array with key 'error', or the key 'offerings' with an array of objects -
     *              each object containing the keys offering_id, start_date, end_date,
     *                                              session_id, course_title, course_id,
     *                                              session_title, session_type_css_class
     */
    public function getOfferingsForNonLearnerDashboard ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');

        $userId = $this->session->userdata('uid');

        // Course Director and Faculty
        $userRoles = array(User_Role::COURSE_DIRECTOR_ROLE_ID, User_Role::FACULTY_ROLE_ID);
        $year = null;
        $includeArchived = false;

        // Retrieve filters params
        $filters = $this->input->get_post('filters');
        if (!empty($filters)) {
            $filters = json_decode($filters);
            if ($filters->showAllActivities) {
                $userId = null;
            }
            $year = $filters->academicYear;
            $includeArchived = true;
        }

        $visualAlertThreshold = $this->config->item('visual_alert_threshold_in_days');
        if (empty($visualAlertThreshold)) { // apply default
            $visualAlertThreshold = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS;
        }

        $offeringArray = $this->queries->getOfferingsForCalendar($schoolId,
                $userId, $userRoles, $year, $includeArchived, $visualAlertThreshold);

        $rhett = array();
        $rhett['offerings'] = $this->_applyFiltersOnOfferings($filters, $offeringArray);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Get all Session Independent Learnings where the user is an instructor who has been
     *      been associated to a SILM either as themselves or as a member of an instructor group
     *
     * @return a JSON'd array with key 'error', or the key 'silms' with an array of objects -
     *              each object containing the keys session_id, due_date, course_title, course_id,
     *                                              session_title, session_type_css_class
     */
    public function getSessionILMsForNonLearnerDashboard ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');

        $userId = $this->session->userdata('uid');
        $userRoles = array( $this->userRole->getRoleId('course director'),
                            $this->userRole->getRoleId('faculty') );
        $year = null;
        $includeArchived = false;

        // Retrieve filters params
        $filters = $this->input->get_post('filters');
        if (!empty($filters)) {
            $filters = json_decode($filters);
            if ($filters->showAllActivities) {
                $userId = null;
            }
            $year = $filters->academicYear;
            $includeArchived = true;
        }

        $rhett = array();

        /* $directorSilms = $this->queries->getSILMsForUserAsCourseDirector($userId); */
        /* $instructorSilms = $this->queries->getSILMsForUserAsInstructor($userId); */
        /* // merge silms */
        /* $silms = $this->_mergeSessionILMs($directorSilms, $instructorSilms); */

        $visualAlertThreshold = $this->config->item('visual_alert_threshold_in_days');
        if (empty($visualAlertThreshold)) { // apply default
            $visualAlertThreshold = Ilios_Config_Defaults::DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS;
        }

        $silms = $this->queries->getSILMsForCalendar($schoolId, $userId,
                $userRoles, $year, $includeArchived, $visualAlertThreshold);
        $rhett['silms'] = $this->_applyFiltersOnOfferings($filters, $silms);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action retrieves and prints the current user's API key. If none exists yet then the key will be created
     * as part of the process.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "key", which contains the API key as its value.
     * On failure, the object contains a property "error", which contains an error message as its value.
     */
    public function getApiKey ()
    {
        $key = $this->authentication->getApiKey($this->session->userdata('uid'));
        if (false !== $key) {
            header('Content-type: text/plain');
            print json_encode(array('key' => $key));
        } else {
            $this->createNewApiKey();
        }
    }

    /**
     * This action updates or creates the API key for the current user.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "key", which contains the API key as its value.
     * On failure, the object contains a property "error", which contains an error message as its value.
     *
     * @todo Refactor the actual key generation algorithm out into a utility class/method. [ST 2013/12/13]
     */
    public function createNewApiKey ()
    {
        $userId = $this->session->userdata('uid');

       $key = Ilios_PasswordUtils::generateToken();

        // check if key already exists.
        if (false === $this->authentication->getApiKey($userId)) {
            // create new key
            $success = $this->authentication->createApiKey($userId, $key);
        } else {
            // update key
            $success = $this->authentication->changeApiKey($userId, $key);
        }

        header('Content-type: text/plain');
        if ($success) {
            print json_encode(array('key' => $key));
        } else {
            print json_encode(array('error' => 'Error'));
        }
    }

    /**
     * @todo add code docs
     * @param string $role
     */
    public function exportICalendar ($role='all')
    {
        $userRoles = array();

        // authorization check and capture of user requested/available user roles
        if ($this->session->userdata('is_learner') && ($role == 'all' || $role == 'student')) {
            $userRoles[] = User_Role::STUDENT_ROLE_ID;
        }
        if ($this->session->userdata('has_instructor_access') && ($role == 'all' || $role == 'instructor')) {
            $userRoles[] = User_Role::FACULTY_ROLE_ID;
            $userRoles[] = User_Role::COURSE_DIRECTOR_ROLE_ID;
        }
        if (! count($userRoles)) {
            header('HTTP/1.1 403 Forbidden'); // VERBOTEN!
            return;
        }

        $userId = $this->session->userdata('uid');

        $schoolId = $this->session->userdata('school_id');

        $events = $this->calendarfeeddataprovider->getData($userId, $schoolId, $userRoles);

        $calendar_title = 'Ilios Calendar';
        $this->icalexporter->setTitle($calendar_title);
        $ical = $this->icalexporter->toICal($events);

        $filename="ilios_calendar.ics";
        header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-length: '.strlen($ical));
        header('Content-type: text/calendar');

        echo $ical;
    }

    /**
     * usort() callback function.
     * Compares two given UNIX timestamps.
     * @param string $a
     * @param string $b
     * @return int
     */
    protected function _offeringStartDateComparator ($a, $b) {
        $startDateA = strtotime($a['start_date']);
        $startDateB = strtotime($b['start_date']);

        return $startDateA - $startDateB;
    }

    /**
     * Merges two lists of offerings while filtering out duplicates.
     *
     * @param array $offerings
     * @param array $additionalOfferings
     * @return array the merged list
     */
    protected function _mergeOfferings ($offerings, $additionalOfferings) {
        // get offering ids
        $fn = create_function('$offering', 'return $offering["offering_id"];'); // callback function
        $offeringIds = array_map($fn, $offerings);
        // merge lists
        foreach ($additionalOfferings as $offering) {
            // filter out duplicate offerings
            if (!in_array($offering['offering_id'], $offeringIds)) {
                $offerings[] = $offering;
            }
        }
        return $offerings;
    }

    /**
     * Merges two lists of session-ILMs (silm) while filtering out duplicates.
     *
     * @param array $sessionILM
     * @param array $additionalSessionILMs
     * @return array the merged list
     */
    protected function _mergeSessionILMs (array $sessionILM, array $additionalSessionILMs) {
        // get session ids
        $fn = create_function('$silm', 'return $silm["session_id"];'); // callback function
        $sessionIds = array_map($fn, $sessionILM);
        // merge lists
        foreach ($additionalSessionILMs as $silm) {
            // filter out duplicate sessions
            if (!in_array($silm['session_id'], $sessionIds)) {
                $sessionILM[] = $silm;
            }
        }
        return $sessionILM;
    }

    /**
     * @todo add docs.
     * @param string $prefName
     * @param string $role
     */
    protected function _setViewPreferenceByRole ($prefName, $role) {
        switch ($role) {
            case 'student' :
                // PARANOID MODE
                // double-check that the current user actually is in the student role
                // before setting the view preference to 'student view'
                if ($this->session->userdata('is_learner')) {
                    $this->session->set_userdata($prefName, 'student');
                }
                break;
            case 'instructor' :
                if ($this->session->userdata('has_instructor_access')) {
                    $this->session->set_userdata($prefName, 'instructor');
                }
                break;
            default:
                // do nothing
        }
    }

    /**
     * Apply filters on an Offering array.
     * @param object $filters
     * @param array  $offerings
     * @param int $overrideSchoolId
     * @return array
     */
    protected function _applyFiltersOnOfferings($filters, $offerings, $overrideSchoolId = null)
    {
        $retarr = array();
        $schoolId = empty($overrideSchoolId) ? $this->session->userdata('school_id') : $overrideSchoolId;

        foreach ($offerings as $offering) {

            // Filter out offerings from other schools. Probably should have done this when we get the offerings from the database.
            $courseid = $offering['course_id'];
            $course = $this->course->getRowForPrimaryKeyId($courseid);

            if ($schoolId != $course->owning_school_id) {
                continue;
            }

            if (!empty($filters)) {
                $courseid = $offering['course_id'];

                if (!empty($filters->academicYear)) {
                    if ($offering['year'] != $filters->academicYear)
                        continue;
                }
                if (!empty($filters->disciplineIds)) {
                    $disciplines = $this->course->getDisciplinesForCourse($courseid);
                    $matched = false;
                    foreach ($disciplines as $discipline) {
                        if (in_array($discipline->discipline_id, $filters->disciplineIds)) {
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched)
                        continue;
                }
                if (!empty($filters->courseIds)) {
                    if (!in_array($courseid, $filters->courseIds))
                        continue;
                }
                if (!empty($filters->sessionTypeIds)) {
                    if (!in_array($offering['session_type_id'], $filters->sessionTypeIds))
                        continue;
                }
                if (!empty($filters->courseLevels)){
                    if (!in_array($offering['course_level'], $filters->courseLevels))
                        continue;
                }
                if (!empty($filters->programCohortIds)) {
                    $matched = false;
                    $cohorts = $this->course->getCohortsForCourse($courseid);
                    foreach ($cohorts as $cohort) {
                        if (in_array($cohort->program_year_id, $filters->programCohortIds)) {
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched)
                        continue;
                }
            }
            array_push($retarr, $offering);
        }

        return $retarr;
    }

    /**
     * Loads the instructor calendar view.
     * @param array $data
     */
    private function _viewInstructorCalendar (array $data = array())
    {
        $this->load->view('home/educator_calendar_view', $data);
    }

    /**
     * Loads the student calendar view.
     * @param array $data
     */
    private function _viewStudentCalendar (array $data = array())
    {
        $this->load->view('home/student_calendar_view', $data);
    }
}

