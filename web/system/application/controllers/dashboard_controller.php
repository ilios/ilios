<?php
include_once "calendar_controller.php";

/**
 * @package Ilios2
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
     * @todo add code docs
     */
    public function index ()
    {
        // authentication check
        if ($this->divertedForAuthentication) {
            return;
        }

        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->i18nVendor;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        $isStudent = $this->session->userdata('is_learner');
        $hasInstructorAccess = $this->session->userdata('has_instructor_access');

        if (! $isStudent && ! $hasInstructorAccess) {
            $this->_viewAccessForbiddenPage($lang, $data);
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

        $userRow = $this->user->getRowForPrimaryKeyId($data['user_id']);

        $data['show_console'] = $this->session->userdata('has_admin_access');


        $schoolTitle = null;

        if ($schoolId) {
            $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);
            if ($schoolRow) {
                $schoolTitle = $schoolRow->title;
            }
        }

        $data['viewbar_title'] = $data['institution_name'];

        if ($schoolTitle != null) {
            $key = 'general.phrases.school_of';
            $schoolOfStr = $this->i18nVendor->getI18NString($key, $lang);
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
                $data['select_school_string'] = $this->i18nVendor->getI18NString($key, $lang);
            }
        }

        // get school competencies
        $schoolCompetencies = $this->_getSchoolCompetencies();
        $data['school_competencies'] = Ilios_Json::encodeForJavascriptEmbedding($schoolCompetencies,
            Ilios_Json::JSON_ENC_SINGLE_QUOTES);

        $key = 'dashboard.account_mgmt';
        $data['account_management_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.competency';
        $data['competency_mapping_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.my_alerts';
        $data['my_alerts_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.my_calendar';
        $data['my_calendar_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.my_courses';
        $data['my_courses_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.my_programs';
        $data['my_programs_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.my_reports';
        $data['my_reports_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.recent_activities';
        $data['recent_activities_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.title';
        $data['title_bar_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.page_header.course_developer';
        $data['page_title_course_developer_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.page_header.student';
        $data['page_title_student_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.view_public';
        $data['view_public_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.administration.course_rollover';
        $data['course_rollover_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.administration.management_console';
        $data['management_console_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.reminder.mark_complete';
        $data['mark_complete_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.reminder.max_chars';
        $data['max_char_string'] = strtolower($this->i18nVendor->getI18NString($key, $lang));

        $key = 'dashboard.reminder.your_alert';
        $data['your_alert_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.report.association';
        $data['report_association_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.report.header';
        $data['report_header_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.report.title';
        $data['report_title_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.report.report_title_optional';
        $data['report_title_optional_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'dashboard.icalendar.download_title';
        $data['ical_download_title'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.add_new';
        $data['phrase_add_new_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.advanced_search';
        $data['phrase_advanced_search_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.due_date';
        $data['phrase_due_date_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.end_time';
        $data['phrase_end_time_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.search_ilios';
        $data['phrase_search_ilios_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.start_time';
        $data['phrase_start_time_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.student_group';
        $data['phrase_student_group_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.time_range';
        $data['phrase_time_range_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.welcome_back';
        $data['phrase_welcome_back_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.phrases.which_is';
        $data['phrase_which_is_string'] = strtolower($this->i18nVendor->getI18NString($key, $lang));

        $key = 'general.terms.none';
        $data['word_none_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.all';
        $data['word_all_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.administration';
        $data['word_administration_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.archiving';
        $data['word_archiving_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.cancel';
        $data['word_cancel_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.course';
        $data['word_course_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.created';
        $data['word_created_string'] = strtolower($this->i18nVendor->getI18NString($key, $lang));

        $key = 'general.terms.date';
        $data['word_date_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.done';
        $data['word_done_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.event';
        $data['word_event_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.filter';
        $data['word_filter_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.help';
        $data['word_help_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.instructors';
        $data['word_instructors_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.instructor_indefinite';
        $data['word_instructors_indefinite_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.room';
        $data['word_room_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.session';
        $data['word_session_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.status';
        $data['word_status_string'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'general.terms.weeks';
        $data['word_weeks_string'] = strtolower($this->i18nVendor->getI18NString($key, $lang));

        $key = 'learning_material.dialog.title';
        $data['learning_materials_dialog_title'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'mesh.dialog.search_mesh';
        $data['mesh_search_mesh']= $this->i18nVendor->getI18NString($key, $lang);

        $key = 'mesh.dialog.title';
        $data['mesh_dialog_title']= $this->i18nVendor->getI18NString($key, $lang);

        $data['preference_array'] = $this->getPreferencesArrayForUser();

        $data['render_headerless'] = false;
        $data['show_view_switch'] = false;

        $key = 'calendar.ical';
        $data['ical_download_button'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'calendar.filters_set_filters';
        $data['calendar_filters_btn'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'calendar.filters_title';
        $data['calendar_filters_title'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'calendar.filters_clear_search';
        $data['calendar_clear_search_filters'] = $this->i18nVendor->getI18NString($key, $lang);

        $key = 'calendar.filters_search_mode_title';
        $data['calendar_search_mode_title'] = $this->i18nVendor->getI18NString($key, $lang);

        $fdata = array();   // Data for calendar filter's content
        $fdata['calendar_filters_title'] = $data['calendar_filters_title'];
        $fdata['search_by_course_text'] = $this->i18nVendor->getI18NString('calendar.filters_search_by_course_text',
                                                                           $lang);
        $fdata['search_by_topic_text'] = $this->i18nVendor->getI18NString('calendar.filters_search_by_topic_text',
                                                                          $lang);
        $fdata['academic_year_title'] = $this->i18nVendor->getI18NString('calendar.filters_academic_year_title',
                                                                          $lang);

        $fdata['discipline_titles'] = $this->discipline->getAllDisciplineTitles($schoolId);
        $fdata['session_type_titles'] = $this->sessionType->getSessionTypeTitles($schoolId);
        // Currently course levels are hard coded in course_container_include.php
        $level = $this->i18nVendor->getI18NString('general.terms.level', $lang);
        $fdata['course_levels'] = array( 1 => "$level I",
                                         2 => "$level II",
                                         3 => "$level III",
                                         4 => "$level IV",
                                         5 => "$level V");

        //$fdata['course_levels'] = array(1 => 'Level I', 2 => 'Level II', 3 => 'Level III', 4 => 'Level IV', 5 => 'Level V');
        $programcohorts = $this->programYear->getAllProgramCohortsWithSchoolId($schoolId);
        if (!empty($programcohorts)) {
            $fdata['program_cohort_titles'] = array_combine(array_keys($programcohorts),
                                                            array_map(create_function( '$n', 'return $n["program_cohort_title"];'), $programcohorts));
            asort($fdata['program_cohort_titles']);
        } else {
            $fdata['program_cohort_titles'] = $programcohorts;
        }

        $fdata['course_titles'] = $this->course->getAllCourseTitles();
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
                    $data['switch_to_student_view_string']= $this->i18nVendor->getI18NString($key, $lang);
                    $this->_viewInstructorDashboard($data);
                    break;
                case 'student' :
                default :
                    $key = 'dashboard.switch_to_instructor_view';
                    $data['switch_to_instructor_view_string']= $this->i18nVendor->getI18NString($key, $lang);
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
     * Controller action.
     *
     * Sets the preferred view on request
     * and stores this preference in the current user session.
     *
     * Expected request parameters:
     *    "preferred_view" ... either "student or "instructor",
     *        depending on what view the users wants to switch to
     *
     * @see Calendar_Controller::switchView()
     */
    public function switchView ()
    {
        // authentication check
        if ($this->divertedForAuthentication) {
            return;
        }

        $role = $this->input->get('preferred_view', false);
        $this->_setViewPreferenceByRole('dashboard_view', $role);
        redirect('/dashboard_controller'); // redirect to itself
    }

    /**
     * XHR handler.
     * Adds or updates given user reminders.
     * @todo flesh out docbloc
     */
    public function addOrUpdateReminder ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $reminderId = $this->input->get_post('reminder_id');
        $noteText = $this->input->get_post('note');
        // scrub the note text
        $noteText = Ilios_CharEncoding::convertToUtf8($noteText);
        $noteText = Ilios_CharEncoding::utf8UrlDecode($noteText);
        $dueDate = $this->input->get_post('due');
        $closed = ($this->input->get_post('closed') == 'true');


        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $this->reminder->startTransaction();
            $newReminderId = $this->reminder->saveReminder($reminderId, $noteText, $dueDate, $closed);

            $rhett = array();

            if ((! $newReminderId) || ($newReminderId < 1) || $this->reminder->transactionAtomFailed()) {
                $lang =  $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);
                $rhett['error'] = $msg;
                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->reminder);
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
     * @todo add code docs
     */
    public function loadReminders ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $rhett['reminders'] = $this->reminder->loadAllRemindersForCurrentUserForFollowingDays(7);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function addReport ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $subjectTable = $this->input->get_post('noun1');
        $prepositionalObjectTable = $this->input->get_post('noun2');
        $poValues = explode(',', $this->input->get_post('noun2_values'));
        $title = $this->input->get_post('title');

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            $this->report->startTransaction();

            $newReportId = $this->report->saveReport($subjectTable, $prepositionalObjectTable,
                                                     $poValues, $title);

            $rhett = array();

            if ((! $newReportId) || ($newReportId < 1)
                                 || $this->report->transactionAtomFailed()) {
                $lang =  $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('general.error.db_insert', $lang);

                $rhett['error'] = $msg;

                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->report);
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
     * @todo add code docs
     */
    public function deleteReport ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $reportId = $this->input->get_post('rid');

        $failedTransaction = true;
        $transactionRetryCount = Abstract_Ilios_Controller::$DB_TRANSACTION_RETRY_COUNT;
        do {
            unset($rhett['error']);

            $this->report->startTransaction();

            if ($this->report->deleteReport($reportId)
                                                    && (! $this->report->transactionAtomFailed())) {
                $this->report->commitTransaction();

                $failedTransaction = false;

                $rhett['success'] = 'hurrah';
            }
            else {
                $lang =  $this->getLangToUse();
                $msg = $this->i18nVendor->getI18NString('general.error.db_update', $lang);

                $rhett['error'] = $msg;

                $this->failTransaction($transactionRetryCount, $failedTransaction, $this->report);
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function loadReports ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $rhett['reports'] = $this->report->getAllReports($this->session->userdata('uid'));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function runReport ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $allowProgramYearArchiving = $this->input->get_post('py_archive');
        $allowCourseArchiving = $this->input->get_post('course_archive');
        $allowCourseRollover = $this->input->get_post('course_rollover');

        $this->session->set_userdata('py_archiving', ($allowProgramYearArchiving == 'true'));
        $this->session->set_userdata('course_archiving', ($allowCourseArchiving == 'true'));

        $rhett['prefs'] = $this->getPreferencesArrayForUser();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function setRolloverPreference ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $allowCourseRollover = $this->input->get_post('course_rollover');

        $this->session->set_userdata('course_rollover', ($allowCourseRollover == 'true'));

        $rhett['prefs'] = $this->getPreferencesArrayForUser();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function getRecentActivity ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
     *  @todo add code docs.
     */
    public function getAllInstructorGroupsForReportSelection ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
     * XHR Handler
     * Prints out a JSON-formatted list of available programs for reporting.
     */
    public function getAllProgramsForReportSelection ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
     * @todo add code docs
     */
    public function getAllProgramYearsForReportSelection ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $rhett['items'] = $this->programYear->getProgramYears();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Prints out a JSON-formatted array of available courses for reporting.
     */
    public function getAllCoursesForReportSelection ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
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
     * @todo add code docs.
     */
    public function getAllSessionsForReportSelection ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $rhett['items'] = $this->iliosSession->getSessionsWithCourseTitle();

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    public function getProgramsForCourses ()
    {
        $rhett = array();
        $lang =  $this->getLangToUse();

        // authentication check
        if ($this->divertedForAuthentication) {
            $this->_printAuthenticationFailedXhrResponse($lang);
            return;
        }

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $courseIdString = $this->input->get_post('course_ids');
        $courseIdArray = preg_split("/,/", $courseIdString);

        $rhett['programs'] = $this->queries->getProgramsForCourseIds($courseIdArray);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }


    /**
     * Loads the instructor dashboard view.
     * @param array $data
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
                $this->i18nVendor->getI18NString('dashboard.administration.has_student_sync_exceptions_label', $data['lang']);
        }
        if ($data['has_non_student_sync_exceptions']) {
            $data['sync_exceptions_indicators'][] =
                $this->i18nVendor->getI18NString('dashboard.administration.has_non_student_sync_exceptions_label', $data['lang']);
        }

        // load view
        $this->load->view('home/course_developer_dashboard_view', $data);
    }

    /**
     * Loads the student dashboard view.
     * @param array $data
     */
    private function _viewStudentDashboard (array $data = array())
    {
        $this->load->view('home/student_dashboard_view', $data);
    }

}
