<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "calendar_controller.php";

/**
 * @package Ilios
 *
 * This is the user dashboard controller.
 * It extends the user calendar controller.
 */
class Dashboard_Controller extends Calendar_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('User_Sync_Exception', 'syncException', true);
    }

    /**
     * Default action.
     * Prints the user dashboard.
     *
     * Accepts the following request parameters:
     *     'schoolselect' ... (optional) The id of the currently selected "active" school.
     *         If provided, then the active school id in the user-session is updated with the given value.
     *     'stripped_view' ... (optional) If any value is provided then the student-dashboard is printed.
     *
     * Prints the full dashboard page.
     * @todo Remove the "stripped_view" option.
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

        $data['show_console'] = $this->session->userdata('has_admin_access');

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

        $key = 'dashboard.page_header.educator';
        $data['page_title_educator_string'] = $this->languagemap->getI18NString($key);

        $key = 'dashboard.page_header.student';
        $data['page_title_student_string'] = $this->languagemap->getI18NString($key);

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

        $key = 'dashboard.report.report_title_optional';
        $data['report_title_optional_string'] = $this->languagemap->getI18NString($key);

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

        $key = 'general.terms.none';
        $data['word_none_string'] = $this->languagemap->getI18NString($key);

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

        $data['user_preferences_json'] = json_encode($this->_getUserPreferences());

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

        // render view
        // TODO refactor this out into its own function.

        // 1. "stripped view"
        // if requested, the user gets the student's view of the dashboard
        // (sans title and welcome text).
        // this simple rule trumps any role-based display constraints,
        // hence we deal with it first.
        if ($this->input->get_post('stripped_view') != null) {
            $data['render_headerless'] == true;
            $this->_viewStudentDashboard($data);
            return;
        }

        // 2. role-specific view.
        if ($isStudent && $hasInstructorAccess) { // user is both student and has instructor-level access
            // set the dashboard view based on the preferences set in the user session.
            // by default, show the "student view" of the dashboard.
            $dashboardView = $this->session->userdata('dashboard_view');
            $data['show_view_switch'] = true;

            switch ($dashboardView) {
                case 'instructor' :
                    $key = 'dashboard.switch_to_student_view';
                    $data['switch_to_student_view_string']= $this->languagemap->getI18NString($key);
                    $this->_viewInstructorDashboard($data);
                    break;
                case 'student' :
                default :
                    $key = 'dashboard.switch_to_instructor_view';
                    $data['switch_to_instructor_view_string']= $this->languagemap->getI18NString($key);
                    $this->_viewStudentDashboard($data);
            }
            return;
        } elseif ($isStudent) { // user is learner only
            $this->_viewStudentDashboard($data);
            return;
        } else  { // user has instructor-level access only
            $this->_viewInstructorDashboard($data);
        }
    }

    /**
     * Toggles between the view between the student- and educator-dashboard.
     * The requested view-selection is persisted in the user-session.
     *
     * Accepts the following query string parameters:
     *    "preferred_view" ... either "student or "instructor",
     *        depending on what view the users wants to switch to
     *
     * Redirects to the user dashboard.
     *
     * @see Calendar_Controller::switchView()
     * @see Dashboard_Controller::index()
     */
    public function switchView ()
    {
        $role = $this->input->get('preferred_view', false);
        $this->_setViewPreferenceByRole('dashboard_view', $role);
        redirect('/dashboard_controller'); // redirect to itself
    }

    /**
     * Adds or updates given user reminders.
     *
     * Accepts the following POST parameters:
     *     "reminder_id" ... The record ID of the reminder that is to be updated.
     *         If none is given, then a new reminder will be created.
     *     "note" ... The reminder text.
     *     "due" ... The reminder's due date.
     *     "closed" ... A flag indicating whether the reminder is closed. Either "true" or "false".
     *
     * Prints out the new or updated reminder as JSON-formatted text.
     *
     * @todo this should be two separate actions, one for creating and one for updating reminders.
     */
    public function addOrUpdateReminder ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $reminderId = $this->input->get_post('reminder_id');
        $noteText = $this->input->get_post('note');
        // scrub the note text
        $noteText = Ilios_CharEncoding::utf8UrlDecode($noteText);
        $dueDate = $this->input->get_post('due');
        $closed = ($this->input->get_post('closed') == 'true');


        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $this->reminder->startTransaction();
            $newReminderId = $this->reminder->saveReminder($reminderId, $noteText, $dueDate, $closed, $userId);

            $rhett = array();

            if ((! $newReminderId) || ($newReminderId < 1) || $this->reminder->transactionAtomFailed()) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');
                $rhett['error'] = $msg;
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->reminder);
            } else {
                $rhett['reminder_id'] = $newReminderId;
                $failedTransaction = false;
                $this->reminder->commitTransaction();
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Retrieves and prints reminders for the current user that a due within seven days from now.
     *
     * Prints out an array of reminders as JSON-formatted text.
     *
     * @todo Change the hard-wired seven-day threshold to a configurable value in the application settings.
     */
    public function loadReminders ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $rhett['reminders'] = $this->reminder->loadAllRemindersForCurrentUserForFollowingDays(7, $userId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Adds a new report.
     *
     * Accepts the following POST parameters:
     *     "noun1" ... The name of the subject table.
     *     "noun2" ... The name of the prepositional object table.
     *     "noun2_values" ... A comma separated list of prepositional object values.
     *     "title" ... (optional) The report title.
     *
     * Prints out an result-array as JSON-formatted text.
     * On success, the result-array will contain the record ID of the newly created report, keyed off by "report_id".
     * On failure, the result-array will contain an error message, keyed off by "error".
     *
     */
    public function addReport ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $subjectTable = $this->input->get_post('noun1');
        $prepositionalObjectTable = $this->input->get_post('noun2');
        $poValues = explode(',', $this->input->get_post('noun2_values'));
        $title = $this->input->get_post('title');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $this->report->startTransaction();

            $newReportId = $this->report->saveReport($subjectTable, $prepositionalObjectTable,
                                                     $poValues, $title, $userId);

            $rhett = array();

            if ((! $newReportId) || ($newReportId < 1)
                                 || $this->report->transactionAtomFailed()) {
                $msg = $this->languagemap->getI18NString('general.error.db_insert');

                $rhett['error'] = $msg;

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->report);
            }
            else {
                $rhett['report_id'] = $newReportId;

                $this->report->commitTransaction();

                $failedTransaction = false;
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Deletes a report.
     *
     * Accepts the following POST parameters:
     *     "rid" ... The id of the report to be deleted.
     *
     * Prints out an result-array as JSON-formatted text.
     * On success, the result-array will contain a success message, keyed off by "success".
     * On failure, the result-array will contain an error message, keyed off by "error".
     */
    public function deleteReport ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $reportId = $this->input->get_post('rid');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            unset($rhett['error']);
            $this->report->startTransaction();

            if ($this->report->deleteReport($reportId) && (! $this->report->transactionAtomFailed())) {
                $this->report->commitTransaction();
                $failedTransaction = false;
                $rhett['success'] = 'hurrah';
            } else {
                $msg = $this->languagemap->getI18NString('general.error.db_update');
                $rhett['error'] = $msg;
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->report);
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Retrieves and prints a list of reports for the current user.
     *
     * Prints out an array of reports as JSON-formatted text.
     */
    public function loadReports ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $rhett['reports'] = $this->report->getAllReports($userId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function runReport ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $reportId = $this->input->get_post('report_id');
        $schoolId = $this->session->userdata('school_id');

        $rhett = $this->report->runReport($reportId, $schoolId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @return This presently may return heterogeneous objects (all objects will have at least
     *              program_id and title attributes) - some objects may contain duplicate info.
     */
    public function getProgramsForUserAsDirector ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $programYears = $this->programYear->getProgramYearsForDirector($this->session->userdata('uid'));

        $programs = array();
        foreach ($programYears as $programYear) {
            $program = $this->program->getRowForPrimaryKeyId($programYear->program_id);
            if ($program->owning_school_id == $this->session->userdata('school_id')) {
                array_push($programs, $this->convertStdObjToArray($program));
            }
        }

        $courses = $this->course->getCoursesForDirector( $this->session->userdata('uid'),
                                                         $this->session->userdata('school_id') );
        $courseIdArray = array();
        foreach ($courses as $course) {
            array_push($courseIdArray, $course->course_id);
        }
        $programsFromCourses = $this->queries->getProgramsForCourseIds($courseIdArray);
        foreach ($programsFromCourses as $program) {
            array_push($programs, $program);
        }

        $rhett['programs'] = $programs;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function getCoursesForUserAsDirector ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $rhett['courses'] = $this->course->getCoursesForDirector( $this->session->userdata('uid'),
                                                                  $this->session->userdata('school_id') );

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function setArchivingPreferences ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $allowProgramYearArchiving = $this->input->get_post('py_archive');
        $allowCourseArchiving = $this->input->get_post('course_archive');

        $this->session->set_userdata('py_archiving', ($allowProgramYearArchiving == 'true'));
        $this->session->set_userdata('course_archiving', ($allowCourseArchiving == 'true'));

        $rhett['prefs'] = $this->_getUserPreferences();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function setRolloverPreference ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $allowCourseRollover = $this->input->get_post('course_rollover');

        $this->session->set_userdata('course_rollover', ($allowCourseRollover == 'true'));

        $rhett['prefs'] = $this->_getUserPreferences();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function getRecentActivity ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $rhett['events'] = $this->queries->getMostRecentAuditEventsForUser($this->session->userdata('uid'),
            $this->session->userdata('school_id'));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Prints out a JSON-formatted array of available disciplines for reporting.
     */
    public function getAllDisciplinesForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $queryResults = $this->discipline->getDisciplinesFilteredOnTitleMatch('', $schoolId);
        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $item = array();
            $item['value'] = $row['discipline_id'];
            $item['display_title'] = $row['title'];

            array_push($items, $item);
        }

        $rhett['items'] = $items;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     *  Prints out a JSON-formatted array of instructor groups for reporting.
     */
    public function getAllInstructorGroupsForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $queryResults = $this->instructorGroup->returnRowsFilteredOnTitleMatch('');
        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $schoolRow = $this->school->getRowForPrimaryKeyId($row['school_id']);

            $item = array();
            $item['value'] = $row['instructor_group_id'];
            $item['display_title'] = $row['title'] . ' (School of ' . $schoolRow->title . ')';

            array_push($items, $item);
        }

        $rhett['items'] = $items;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints out a JSON-formatted array of available programs for reporting.
     */
    public function getAllProgramsForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $uid = $this->session->userdata('uid');

        $queryResults = $this->program->getProgramsFilteredOnTitleMatch('', $schoolId, $uid);
        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $item = array();
            $item['value'] = $row['program_id'];
            $item['display_title'] = $row['title'];

            array_push($items, $item);
        }

        $rhett['items'] = $items;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints out a JSON-formatted array of available program years for reporting.
     */
    public function getAllProgramYearsForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');

        $rhett['items'] = $this->programYear->getProgramYears($schoolId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Prints out a JSON-formatted array of available courses for reporting.
     */
    public function getAllCoursesForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $uid = $this->session->userdata('uid');
        $queryResults = $this->course->getCoursesFilteredOnTitleMatch('', $schoolId, $uid);

        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $item = array();

            $item['value'] = $row['course_id'];

            $displayString = $row['title'] . ' - ';

            $strToTime = strtotime($row['start_date']);
            $displayString .= date('n/j/Y', $strToTime) . '-';

            $strToTime = strtotime($row['end_date']);
            $displayString .= date('n/j/Y', $strToTime);

            $item['display_title'] = $displayString;

            array_push($items, $item);
        }

        $rhett['items'] = $items;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }
    /**
     * Prints out a JSON-formatted array of available sessions for reporting.
     */
    public function getAllSessionsForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $rhett['items'] = $this->iliosSession->getSessionsWithCourseTitle($schoolId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Retrieves and prints a JSON-formatted array of session types for reporting.
     *
     * The returned array contains a single item keyed by "items".
     * The value of "items" is a nested array of assoc. arrays, each sub-array containing the actual
     * session type properties keyed by "value" and "display_title".
     *
     * Example output:
     * <code>
     * {"items": [
     *     {
     *          "value": "109",
     *          "display_title": "Case-Based Instruction\/Learning"
     *     },
     *     {
     *          "value": "110",
     *          "display_title": "Ceremony"
     *     }
     * ]}
     * </code>
     */
    public function getAllSessionTypesForReportSelection ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        // get the school id
        $schoolId = $this->session->userdata('school_id');

        $sessionTypes = $this->sessionType->getList ($schoolId);

        $items = array();
        foreach ($sessionTypes as $row) {
            $item = array();
            $item['value'] = $row['session_type_id'];
            $item['display_title'] = $row['title'];

            array_push($items, $item);
        }

        $rhett['items'] = $items;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @deprecated
     * @todo not used - remove
     */
    public function getProgramsForCourses ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseIdString = $this->input->get_post('course_ids');
        $courseIdArray = preg_split("/,/", $courseIdString);

        $rhett['programs'] = $this->queries->getProgramsForCourseIds($courseIdArray);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }


    /**
     * Loads and prints the instructor dashboard view.
     * Processes the given data array before passing it on to the view for rendering.
     * @param array $data The data to be passed to the view for rendering.
     */
    private function _viewInstructorDashboard (array $data = array())
    {
        // load instructor groups
        $instructorGroups = array();
        $schoolIds = $this->school->getAllSchools();
        foreach ($schoolIds as $schoolId) {
            $instructorGroups = array_merge($instructorGroups, $this->instructorGroup->getModelArrayForSchoolId($schoolId));
        }
        $data['instructor_groups'] = $instructorGroups;

        // get faculties (?)
        $faculty = array();
        $queryResult = $this->getFacultyFilteredOnNameMatch('');
        foreach ($queryResult->result_array() as $row) {
            array_push($faculty, $this->convertStdObjToArray($row));
        }
        $data['faculty'] = $faculty;


        // add info about user synchronization exceptions
        $data['has_non_student_sync_exceptions'] = false;
        $data['has_student_sync_exceptions'] = false;
        $data['sync_exceptions_indicators'] = array();

        $schoolId = $this->session->userdata('school_id'); // scope it down on the current user's school
        if ($schoolId) {
            $data['has_non_student_sync_exceptions'] = $this->syncException->hasNonStudentSyncExceptions($schoolId);
            $data['has_student_sync_exceptions'] = $this->syncException->hasStudentSyncExceptions($schoolId);
        }
        if ($data['has_student_sync_exceptions']) {
            $data['sync_exceptions_indicators'][] =
                $this->languagemap->getI18NString('dashboard.administration.has_student_sync_exceptions_label');
        }
        if ($data['has_non_student_sync_exceptions']) {
            $data['sync_exceptions_indicators'][] =
                $this->languagemap->getI18NString('dashboard.administration.has_non_student_sync_exceptions_label');
        }

        // load view
        $this->load->view('home/educator_dashboard_view', $data);
    }

    /**
     * Loads and prints the student dashboard view.
     * @param array $data The data to be passed to the view for rendering.
     */
    private function _viewStudentDashboard (array $data = array())
    {
        $this->load->view('home/student_dashboard_view', $data);
    }
}
