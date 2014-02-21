<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Course management controller.
 */
class Course_Management extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Alert', 'alert', true);
        $this->load->model('Group', 'group', true);
        $this->load->model('Instructor_Group', 'instructorGroup', true);
        $this->load->model('Program', 'program', true);
        $this->load->model('School', 'school', true);
        $this->load->model('User', 'user', true);
        $this->load->model('Course_Clerkship_Type', 'clerkshipType', true);
    }

    /**
     * Required POST or GET parameters:
     */
    public function index ()
    {
        $data = array();

        // authorization check
        if (!$this->session->userdata('has_instructor_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $this->output->set_header('Expires: 0');

        $key = 'course_management.title_bar';
        $data['title_bar_string'] = $this->languagemap->getI18NString($key);

        $courseId = $this->input->get_post('course_id');
        if ($courseId != '') {
            $data['course_id'] = $courseId;
            $data['session_id'] = $this->input->get_post('session_id');
            if ($data['session_id'] == '') {
                $data['session_id'] = -1;
            }

            $courseRow = $this->course->getRowForPrimaryKeyId($courseId);

            $data['course_title'] = $courseRow->title;
            if (! is_null($courseRow->external_id)) {
                $data['external_id'] = $courseRow->external_id;
            }
            else {
                $data['external_id'] = '';
            }
            $data['course_unique_id'] = $this->course->getUniqueId($courseId);
            $data['course_start_date'] = $courseRow->start_date;
            $data['course_end_date'] = $courseRow->end_date;
            $data['course_year'] = $courseRow->year;
            $data['course_course_level'] = $courseRow->course_level;
            $data['course_is_locked'] = $courseRow->locked;

            if (($courseRow->publish_event_id == null) || ($courseRow->publish_event_id == -1)) {
                $data['course_publish_event_id'] = '';
                $data['course_published_as_tbd'] = false;
            }
            else {
                $data['course_publish_event_id'] = $courseRow->publish_event_id;
                $data['course_published_as_tbd'] = ($courseRow->published_as_tbd == 1);
            }
        }
        else {
            $data['course_id'] = -1;
            $data['session_id'] = -1;
        }

        $userRow = $this->user->getRowForPrimaryKeyId($this->session->userdata('uid'));

        $data['admin_user_short_name'] = $userRow->first_name . ' ' . $userRow->last_name;

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


        $key = 'course_management.add_course.title';
        $data['add_new_course_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.add_course';
        $data['add_course_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.add_objective';
        $data['add_objective_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.add_session';
        $data['add_session_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.collapse_all';
        $data['collapse_sessions_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.current_level';
        $data['current_level_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.external_course_id';
        $data['external_course_id_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.objective_edit_title';
        $data['edit_objective_dialog_title'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.one_per_pc';
        $data['one_per_pc_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.page_header';
        $data['page_header_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.select_cohorts';
        $data['select_cohorts_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.select_parent_objectives';
        $data['select_parent_objectives_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.search.clear';
        $data['generic_search_clear'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.search.title';
        $data['course_search_title'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.search.hint';
        $data['generic_search_hint'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.session.sort.alpha_asc';
        $data['sort_alpha_asc'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.default_order';
        $data['sort_default_ordering'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.session.sort.alpha_asc';
        $data['sort_alpha_asc'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.session.sort.alpha_desc';
        $data['sort_alpha_desc'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.session.sort.date_asc';
        $data['sort_date_asc'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.session.sort.date_desc';
        $data['sort_date_desc'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.multiple_offerings_generator.parent_group_strategy';
        $data['phrase_parent_group_strategy'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.multiple_offerings_generator.sub_group_strategy';
        $data['phrase_sub_group_strategy'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.strategy';
        $data['term_strategy'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.academic_year';
        $data['phrase_academic_year_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.clerkship_type';
        $data['phrase_clerkship_type_string'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.not_a_clerkship';
        $data['phrase_not_a_clerkship_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.associated_learners';
        $data['phrase_associated_learners_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.course_level';
        $data['phrase_course_level_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.course_name';
        $data['phrase_course_name_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.course_year';
        $data['phrase_course_year_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.end_date';
        $data['phrase_end_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.end_time';
        $data['phrase_end_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.file_size';
        $data['phrase_file_size_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.file_type';
        $data['phrase_file_type_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.learning_materials';
        $data['phrase_learning_materials_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.learning_objectives';
        $data['phrase_learning_objectives_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.mesh_terms';
        $data['phrase_mesh_terms_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.not_recurring';
        $data['phrase_not_recurring_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.owner_role';
        $data['phrase_owner_role_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.program_title';
        $data['phrase_program_title_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.program_cohorts';
        $data['phrase_program_cohort_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.show_course_summary';
        $data['phrase_show_course_summary'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.show_less';
        $data['phrase_show_less_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.phrases.show_more';
        $data['phrase_show_more_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.phrases.start_date';
        $data['phrase_start_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.start_time';
        $data['phrase_start_time_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.student_groups';
        $data['phrase_student_groups_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.upload_date';
        $data['phrase_upload_date_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.add';
        $data['word_add_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.citation';
        $data['word_citation_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.cohort';
        $data['word_cohort_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.competencies';
        $data['word_competencies_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.description';
        $data['word_description_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.directors';
        $data['word_directors_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.topics';
        $data['word_disciplines_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.done';
        $data['word_done_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.file';
        $data['word_file_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.filename';
        $data['word_filename_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.filter';
        $data['word_filter_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.help';
        $data['word_help_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.instructors';
        $data['word_instructors_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.link';
        $data['word_link_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.no';
        $data['word_no_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.notes';
        $data['word_notes_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.or';
        $data['word_or_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.terms.owner';
        $data['word_owner_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.required';
        $data['word_required_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.search';
        $data['word_search_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.status';
        $data['word_status_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.user';
        $data['word_user_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.edit';
        $data['word_edit_string'] = $this->languagemap->getI18NString($key);

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

        $key = 'learning_material.asset.creator';
        $data['learning_materials_asset_creator'] = $this->languagemap->getI18NString($key);

        $key = 'learning_material.asset.title';
        $data['learning_materials_asset_title'] = $this->languagemap->getI18NString($key);

        $key = 'learning_material.dialog.title';//course_management.learning_materials.hide_notes
        $data['learning_materials_dialog_title'] = $this->languagemap->getI18NString($key);

        $key = 'course_management.learning_materials.hide_notes';
        $data['edit_learning_materials_hide_notes'] = $this->languagemap->getI18NString($key);

        $key = 'learning_material.metadata_panel.edit_notes.title';
        $data['edit_learning_material_notes_dialog_title']
                                            = $this->languagemap->getI18NString($key);

        $key = 'learning_material.metadata_panel.title';
        $data['learning_materials_metadata_title'] = $this->languagemap->getI18NString($key);

        $key = 'learning_material.search.show_add_div';
        $data['learning_materials_search_show_add_div']
                                        = $this->languagemap->getI18NString($key);

        $key = 'learning_material.search.title';
        $data['learning_materials_search_title']
                                = strtolower($this->languagemap->getI18NString($key));

        $key = 'mesh.dialog.search_mesh';
        $data['mesh_search_mesh']= $this->languagemap->getI18NString($key);

        $key = 'mesh.dialog.title';
        $data['mesh_dialog_title']= $this->languagemap->getI18NString($key);

        $sessionTypes = $this->sessionType->getList($schoolId);

        $clerkshipTypes = $this->clerkshipType->getMap();

        $data['clerkship_types'] = $clerkshipTypes;

        $data['session_type_array'] = $sessionTypes;

        $data['learning_material_roles'] = $this->learningMaterial->getLearningMaterialUserRoles();
        $data['learning_material_statuses']
                                        = $this->learningMaterial->getLearningMaterialStatuses();


        $data['user_preferences_json'] = json_encode($this->_getUserPreferences());

        $this->load->view('course/course_manager', $data);
    }

    /**
     * XHR callback function.
     * Prints out an JSON-formatted array of all instructors and instructor groups
     * associated with a given course's school and cohorts.
     *
     * Excepts the following POST parameters:
     *     'course_id' ... the course identifier
     */
    public function getAvailableInstructors ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseId = $this->input->get_post('course_id');

        // load the course
        $courseRow = $this->course->getRowForPrimaryKeyId($courseId);

        $cohorts = $this->course->getCohortsForCourse($courseId);
        $schoolIds = $this->getSchoolIdsForCohorts($cohorts);

        // ensure that the instructors from the school that this course belongs to are
        // loaded, regardless of (non)existing cohort associations to the course.
        if (! in_array($courseRow->owning_school_id, $schoolIds)) {
            $schoolIds[] = $courseRow->owning_school_id;
        }

        $instructorGroups = array();
        foreach ($schoolIds as $schoolId) {
            $instructorGroups = array_merge($instructorGroups, $this->instructorGroup->getModelArrayForSchoolId($schoolId));
        }
        $rhett['instructor_groups'] = $instructorGroups;

        $faculty = array();
        $queryResult = $this->getFacultyFilteredOnNameMatch('');
        foreach ($queryResult->result_array() as $row) {
            array_push($faculty, $this->convertStdObjToArray($row));
        }
        $rhett['faculty'] = $faculty;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    public function getStudentGroupTrees ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseId = $this->input->get_post('course_id');

        $cohorts = $this->course->getCohortsForCourse($courseId);
        $rhett = $this->getStudentGroupTreesForCohorts($cohorts);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    public function getRolloverSummaryViewForCourseIdInAcademicYear ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseId = $this->input->get_post('course_id');
        $year = $this->input->get_post('year');

        $rhett = $this->course->getRolloverViewForAcademicYear($courseId, $year);

        $results = $this->learningMaterial->getLearningMaterialsForCourse($courseId);
        if (is_null($results)) {
            $rhett['error'] = 'Unable to fetch learning materials for course.';
        }
        else {
            $rhett['learning_materials'] = $results;
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    public function rolloverCourse ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');
        $schoolId = $this->session->userdata('school_id');
        $courseId = $this->input->get_post('course_id');
        $newYear = $this->input->get_post('year');
        $cloneOfferingsToo = ($this->input->get_post('offerings') == 'true');
        $startDate = $this->input->get_post('start_date');
        $endDate = $this->input->get_post('end_date');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->course->startTransaction();

            $rolloverResult = $this->course->rolloverCourse($courseId, $newYear, $startDate,
                                                            $endDate, $cloneOfferingsToo, $schoolId,
                                                            $auditAtoms);

            if ($this->course->transactionAtomFailed()) {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->course);
            }
            else {
                $this->course->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }

                $failedTransaction = false;

                $rhett['success'] = 'ya';
                $rhett['new_cid'] = $rolloverResult[0];
                $rhett['user_can_view'] = $rolloverResult[1];
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Prints out a JSON-formatted array of courses.
     * Expects the following values to be POSTed:
     * - 'query' ... a title/title-fragment to search courses by
     */
    public function getCourseListForQuery ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $title = $this->input->get_post('query');
        $schoolId = $this->session->userdata('school_id');
        $uid = $this->session->userdata('uid');
        $queryResults = $this->course->getCoursesFilteredOnTitleMatch($title, $schoolId, $uid);

        $rhett = array();
        foreach ($queryResults->result_array() as $row) {
            $row['unique_id'] = $this->course->getUniqueId($row['course_id']);
            array_push($rhett, $row);
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }


    public function getCohortObjectives ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $cohorts = explode(",", $this->input->get_post('cohort_id'));
        $rhett = $this->_getObjectivesForCohorts($cohorts);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }



    /**
     * Called from the course add dialog.
     *
     * Echos out a JSON'd map; on failure cases it will contain one entry with the key being
     *              'error'; on success cases - the map will have 6 entries of keys 'course_id',
     *              'title', 'start_date', 'end_date', 'course_year_start' and 'course_level'. Start
     *              and end dates, and course level, are arbitrary but are passed back so that the
     *              client side model correctly reflects the [arbitrary] database state.
     */
    public function addNewCourse ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');
        $schoolId = $this->session->userdata('school_id');

        $this->load->library('form_validation');

        // TODO i18n error message text
        $this->form_validation->set_rules('new_course_title', 'Course Name', 'trim|required');

        $title = $this->input->get_post('new_course_title');

        if (! $this->form_validation->run()) {
            $msg = $this->languagemap->getI18NString('general.error.data_validation');
            $rhett['error'] = $msg . ": " . validation_errors();
        } else {
            $year = $this->input->get_post('new_academic_year');

            if ($this->course->courseExistsWithTitleAndYear($title, $year)) {
                $msg = $this->languagemap->getI18NString('course_management.error.duplicate_title_year');

                $rhett['error'] = $msg;
            } else {
                $failedTransaction = true;
                $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
                do {
                    $auditAtoms = array();

                    unset($rhett['error']);

                    $this->course->startTransaction();

                    $newId = $this->course->addNewCourse($title, $year, $schoolId, $auditAtoms);

                    if ($this->course->transactionAtomFailed() || ($newId == 0)) {
                        Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->course);
                    } else {
                        $this->course->commitTransaction();

                        // save audit trail
                        $this->auditAtom->startTransaction();
                        $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                        if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                            $this->auditAtom->rollbackTransaction();
                        } else {
                            $this->auditAtom->commitTransaction();
                        }

                        $failedTransaction = false;

                        $row = $this->course->getRowForPrimaryKeyId($newId);

                        $rhett['course_id'] = $newId;
                        $rhett['title'] = $title;
                        $rhett['start_date'] = $row->start_date;
                        $rhett['end_date'] = $row->end_date;
                        $rhett['course_year_start'] = $year;
                        $rhett['course_level'] = $row->course_level;
                        $rhett['unique_id'] = $this->course->getUniqueId($newId);
                    }
                } while ($failedTransaction && ($transactionRetryCount > 0));

                if ($failedTransaction) {
                    $msg = $this->languagemap->getI18NString('general.error.db_insert');
                    $rhett['error'] = $msg;
                }
            }
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     *
     * Expected POST params:
     *      course_id
     *      should_publish
     *      title
     *      start_date
     *      end_date
     *      course_level
     *      cohort
     *      competency
     *      discipline
     *      director
     *      mesh_term
     *      objective
     *
     * Prints a JSON'd array with key 'error' or keys 'publish_event_id',
     * 'objectives' which has a value of an array with 0-N arrays - each with
     * the keys 'dbId' and 'md5'.
     * the latter being the md5 hash of the descriptive text for the objective.
     *
     * @todo clean up code docs
     */
    public function saveCourse ()
    {
        $rhett = array();

        //
        // authorization check
        //
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        //
        // input processing
        //
        $courseId = $this->input->post('course_id');
        $externalId = $this->input->post('external_id');
        $shouldPublish = $this->input->post('should_publish');
        $publishAsTBD = 'true' == $this->input->post('publish_as_tbd') ? 1 : 0;
        $startDate = $this->input->post('start_date');
        $endDate = $this->input->post('end_date');
        $courseLevel = (int) $this->input->post('course_level');
        $clerkshipTypeId = (int) $this->input->post('clerkship_type_id');

        try {
            $cohorts = Ilios_Json::deserializeJsonArray($this->input->post('cohort'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.cohorts');
            return;
        }
        try {
            $disciplines = Ilios_Json::deserializeJsonArray($this->input->post('discipline'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.disciplines');
            return;
        }
        try {
            $directors = Ilios_Json::deserializeJsonArray($this->input->post('director'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.directors');
            return;
        }
        try {
            $meshTerms = Ilios_Json::deserializeJsonArray($this->input->post('mesh_term'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.mesh_terms');
            return;
        }
        try {
            $learningMaterials = Ilios_Json::deserializeJsonArray($this->input->post('learning_materials'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.learning_materials');
            return;
        }
        try {
            $objectives = Ilios_Json::deserializeJsonArray($this->input->post('objective'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.course_save.input_validation.objectives');
            return;
        }

        $title = Ilios_CharEncoding::utf8UrlDecode($this->input->post('title'));

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);
            $publishId = -1;


            $this->course->startTransaction();

            if ($shouldPublish == "true") {
                $publishId = $this->publishEvent->addPublishEvent("course", $courseId,
                                                                  $this->getClientIPAddress(), $userId,
                                                                  $auditAtoms);
            }

            $results = $this->course->saveCourseWithId($courseId, $title,
                $externalId, $startDate, $endDate, $courseLevel, $cohorts,
                $disciplines, $directors, $meshTerms, $objectives,
                $learningMaterials, $publishId, $publishAsTBD, $clerkshipTypeId,
                $auditAtoms);

            if (isset($results['error']) || $this->course->transactionAtomFailed()) {
                $rhett['error'] = $results['error'];

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->course);
            } else {
                $rhett['publish_event_id'] = $publishId;
                $rhett['objectives'] = $results['objectives'];

                $failedTransaction = false;

                $this->course->commitTransaction();

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

    public function lockCourse ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $courseId = $this->input->get_post('course_id');
        $archiveAlso = ($this->input->get_post('archive') == 'true');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->course->startTransaction();

            $this->course->lockOrArchiveCourse($courseId, true, $archiveAlso, $auditAtoms);

            if ($this->course->transactionAtomFailed()) {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->course);
            }
            else {
                $this->course->commitTransaction();

                // save audit trail
                $this->auditAtom->startTransaction();
                $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                    $this->auditAtom->rollbackTransaction();
                } else {
                    $this->auditAtom->commitTransaction();
                }

                $failedTransaction = false;

                $rhett['success'] = 'ya';
            }
        } while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    public function getLearnerGroupIdsAndTitles ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseId = $this->input->get_post('course_id');

        $rhett['learners'] = $this->queries->getLearnerGroupIdAndTitleForCourse($courseId);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Adds or updates a course session, based on posted user input.
     *
     * Expected params:
     *      course_id
     *      session_id
     *      title
     *      session_type_id
     *      is_supplemental
     *      attire_required
     *      equipment_required
     *      cnumber
     *      should_publish
     *      discipline
     *      mesh_term
     *      objective
     *
     * prints out a JSON-formatted array with key 'error' or keys 'publish_event_id', 'objectives' which has a
     *                      value of an array with 0-N arrays - each with the keys 'dbId' and 'md5'
     *                      - the latter being the md5 hash of the descriptive text for the
     *                      objective - 'container' - a passback of the cnumber param value -
     *                      'session_id' - either a passback or a new value if this is the first
     *                      save of the session
     * @todo improve code docs
     */
    public function saveSession ()
    {
        $rhett = array();
        $isIlm = false;

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        //
        // input validation and sanitation
        //
        $courseId = $this->input->post('course_id');
        $school = $this->school->getSchoolByCourseId($courseId);
        // check if course is linked to a school
        // if this is not the case then echo out an error message
        // and be done with it.
        if (empty($school)) {
            $msg = $this->languagemap->getI18NString('course_management.error.session_save');
            $rhett = array();
            $rhett['error'] = $msg;
            header("Content-Type: text/plain");
            echo json_encode($rhett);
            return;
        }

        $userId = $this->session->userdata('uid');

        // check if we're dealing with an ILM
        $ilmHours = $this->input->post('ilm_hours');
        if ($ilmHours) {
            $isIlm = true;
        }

        // deserialize arrays of associated data such as learning materials, objectives etc.
        // fail on first bad input.
        try {
            $disciplines = Ilios_Json::deserializeJsonArray($this->input->post('discipline'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.session_save.input_validation.disciplines');
            return;
        }
        try {
            $meshTerms = Ilios_Json::deserializeJsonArray($this->input->post('mesh_term'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.session_save.input_validation.mesh_terms');
            return;
        }
        try {
            $learningMaterials = Ilios_Json::deserializeJsonArray($this->input->post('learning_materials'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.session_save.input_validation.learning_materials');
            return;
        }
        try {
            $objectives = Ilios_Json::deserializeJsonArray($this->input->post('objective'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('course_management.error.session_save.input_validation.objectives');
            return;
        }

        if ($isIlm) {
            try {
                $ilmInstructors = Ilios_Json::deserializeJsonArray($this->input->post('ilm_instructors'), true);
            } catch (Ilios_Exception $e) {
                $this->_printErrorXhrResponse('course_management.error.session_save.input_validation.ilm_instructors');
                return;
            }
        }

        $sessionId = $this->input->post('session_id');
        $sessionTypeId = $this->input->post('session_type_id');
        $containerNumber = $this->input->post('cnumber');
        $shouldPublish = $this->input->post('should_publish');
        $publishAsTBD = ($this->input->post('publish_as_tbd') == 'true') ? 1 : 0;
        $supplemental = ($this->input->post('is_supplemental') == 'true') ? 1 : 0;
        $attireRequired = ($this->input->post('attire_required') == 'true') ? 1 : 0;
        $equipmentRequired = ($this->input->post('equipment_required') == 'true') ? 1 : 0;
        $ilmHours = $this->input->post('ilm_hours');
        $ilmId = null;
        $ilmDueDate = null;
        $ilmLearners = null;
        if ($isIlm) {
            $ilmId = $this->input->post('ilm_db_id');
            $ilmDueDate = $this->input->post('due_date');
            $ilmLearners = explode(',', $this->input->post('ilm_learners'));
        }

        $title = Ilios_CharEncoding::utf8UrlDecode($this->input->post('title'));

        $description = Ilios_CharEncoding::utf8UrlDecode($this->input->post('description'));

        $learningMaterials = $this->_formatSessionLearningMaterialsFromInput($learningMaterials);

        //
        // input processing
        //
        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $publishId = -1;

            $publishNeedsUpdating = false;
            $newSession = ($sessionId == -1);
            $wasPreviouslyUnpublished
                                = ($newSession || (! $this->iliosSession->isPublished($sessionId)));


            $this->iliosSession->startTransaction();


            if ($shouldPublish == "true") {
                $publishNeedsUpdating = $newSession;

                $publishId = $this->publishEvent->addPublishEvent("session", $sessionId,
                                                                  $this->getClientIPAddress(), $userId,
                                                                  $auditAtoms);
            }

            $deleteOfferings = false;

            if ($isIlm) {
                $deleteOfferings = true;

                if (! $this->iliosSession->saveIndependentLearningFacet($ilmId, $ilmHours,
                                                                        $ilmDueDate, $ilmLearners,
                                                                        $ilmInstructors)) {
                    $msg = $this->languagemap->getI18NString('course_management.error.independent_learning_save');

                    $rhett['error'] = $msg;
                }
            }

            if ($deleteOfferings && (! $newSession) && (! isset($rhett['error']))) {
                if (! $this->offering->deleteOfferingsForSession($sessionId, $auditAtoms)) {
                    $msg = $this->languagemap->getI18NString('course_management.error.offering_deletion');

                    $rhett['error'] = $msg;
                }
            }


            if (! isset($rhett['error'])) {
                if ($newSession) {
                    $results = $this->iliosSession->addSession($courseId, $title, $sessionTypeId,
                                                               $disciplines, $meshTerms,
                                                               $objectives, $supplemental,
                                                               $attireRequired, $equipmentRequired,
                                                               $publishId, $description,
                                                               $learningMaterials, $ilmId,
                                                               $auditAtoms);
                }
                else {
                    $results = $this->iliosSession->updateSession($sessionId, $courseId, $title,
                                                                  $sessionTypeId, $disciplines,
                                                                  $meshTerms, $objectives,
                                                                  $supplemental, $attireRequired,
                                                                  $equipmentRequired, $publishId,
                                                                  $publishAsTBD, $description,
                                                                  $learningMaterials, $ilmId,
                                                                  $auditAtoms);
                }

                if (isset($results['error'])) {
                    $rhett['error'] = $results['error'];
                }
            }

            if (isset($rhett['error'])) {
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->iliosSession);
            } else {
                if ($publishNeedsUpdating) {
                    $this->publishEvent->updatePublishEventTableRowIdColumn($publishId,
                                                                        $results['session_id']);

                    if ($this->publishEvent->transactionAtomFailed()) {
                        $msg = $this->languagemap->getI18NString('course_management.error.offering_deletion');

                        $rhett['error'] = $msg;
                    }
                }

                if (isset($rhett['error'])) {
                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                           $this->iliosSession);
                }
                else {
                    $this->iliosSession->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    /*
                     * what could invoke alert notification here:
                     *  . course is published, session was not published but is now published
                     */
                    if ($wasPreviouslyUnpublished && ($publishId != -1)
                                              && $this->course->isPublished($courseId)) {

                        $this->alert->startTransaction();
                        $msg = $this->alert->addOrUpdateAlert($courseId, 'course', $userId, $school,
                                                       array(Alert::CHANGE_TYPE_SESSION_PUBLISH));
                        if ($this->alert->transactionAtomFailed() || ! is_null($msg)) {
                            $this->alert->rollbackTransaction();
                        } else {
                            $this->alert->commitTransaction();
                        }
                    }

                    $rhett['container'] = $containerNumber;
                    $rhett['session_id'] = $results['session_id'];
                    $rhett['publish_event_id'] = $publishId;
                    $rhett['objectives'] = $results['objectives'];

                    if (! is_null($ilmId)) {
                        $rhett['ilm_db_id'] = $ilmId;
                    }

                    $failedTransaction = false;
                }
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Expected params:
     *      session_id
     *      cnumber
     *
     * @return a JSON'd array with key 'error' or key 'container'- a passback of the cnumber param
     *                      value
     */
    public function deleteSession ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $sessionId = $this->input->get_post('session_id');
        $containerNumber = $this->input->get_post('cnumber');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->iliosSession->startTransaction();

            $results = $this->iliosSession->deleteSession($sessionId, $auditAtoms);

            if (isset($rhett['error']) || $this->iliosSession->transactionAtomFailed()) {
                $rhett['error'] = $results['error'];

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                       $this->iliosSession);
            }
            else {
                if (! $this->offering->deleteOfferingsForSession($sessionId, $auditAtoms)) {
                    $rhett['error'] = $this->languagemap->getI18NString('general.error.db_delete');

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction,
                                           $this->iliosSession);
                }
                else {
                    $this->iliosSession->commitTransaction();

                    $failedTransaction = false;

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    $rhett['container'] = $containerNumber;
                }
            }
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Expected POSTed params:
     *  session_id
     *  cnumber
     *  start_date
     *  end_date
     *  is_recurring
     *  gids                    group ids
     *  recurring_event         (if is_recurring == 'true') JSON'd recurring event model object
     *
     * Prints a JSON'd array with key 'error', or the key 'container' - a passback of the
     *                      cnumber param value - and the key 'added' - the number of offerings
     *                      added
     * @todo clean up code docs.
     */
    public function multiOfferingSave ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $sessionId = $this->input->post('session_id');
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

        $containerNumber = $this->input->post('cnumber');
        $startDate = $this->input->post('start_date');
        $endDate = $this->input->post('end_date');

        $groupIds = explode(',', $this->input->post('gids'));

        $recurringEvent = null;
        if ('true' == $this->input->post('is_recurring')) {
            $recurringEvent = $this->input->post('recurring_event');
            $recurringEvent = Ilios_CharEncoding::utf8UrlDecode($recurringEvent);
            try {
                $recurringEvent = Ilios_Json::decode($recurringEvent, true);
            } catch (Ilios_Exception $e) {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.data_validation');
                header("Content-Type: text/plain");
                echo json_encode($rhett);
                return;
            }
        }

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;

        $alertChangeTypes = array(Alert::CHANGE_TYPE_NEW_OFFERING);

        do {
            $counter = 0;

            $auditAtoms = array();

            $this->offering->startTransaction();

            unset($rhett['error']);

            foreach ($groupIds as $groupId) {
                $studentGroupArray = array();
                array_push($studentGroupArray, $groupId);

                $results = $this->offering->saveOffering('', $startDate, $endDate, null,
                                                         $studentGroupArray, $sessionId,
                                                         $recurringEvent, -1, $auditAtoms, null);

                $offeringId = $results['offering_id'];

                if ($this->offering->transactionAtomFailed() || ($offeringId == -1)) {
                    $msg = $this->languagemap->getI18NString('general.error.db_insert');
                    $rhett['error'] = $msg;
                    break;
                }

                if ($sessionIsPublished) {
                    $msg = $this->alert->addOrUpdateAlert($offeringId, 'offering', $userId, $school, $alertChangeTypes);
                    if (! is_null($msg)) {
                        $rhett['error'] = $msg;
                        break;
                    }
                    if ($this->alert->transactionAtomFailed()) {
                        $msg = $this->languagemap->getI18NString('general.error.db_insert');
                        $rhett['error'] = $msg;
                        break;
                    }
                }

                $counter++;
            }

            if (isset($rhett['error'])) {
                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->offering);
            }
            else {
                $rhett['added'] = $counter;
                $rhett['container'] = $containerNumber;

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
        }
        while ($failedTransaction && ($transactionRetryCount > 0));

        if ($failedTransaction && (! isset($rhett['error']))) {
            $msg = $this->languagemap->getI18NString('general.error.db_insert');
            $rhett['error'] = $msg;
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * (non-PHPdoc)
     */
    public function getCourseTree ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $courseId = $this->input->get_post('course_id');

        $rhett = $this->_buildCourseTree($courseId, false, false);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * Re-formats given session learning materials (as they were decoded from
     * user input) for further processing.
     * @param array $raw the unformatted learning materials
     * @return array a nested array of arrays, each sub-array representing a session/learning material association.
     * Each subarray may contain data as key/value pairs with the following keys:
     * 'dbId' ... the learning material key (int|NULL)
     * 'meshTerms' ... a list of associated mesh term descriptor ids (array|NULL)
     * 'required' ... flag indicating whether the material is required or not (boolean)
     * 'notesArePubliclyViewable' ... flag indicating whether the given notes are publ. visible (boolean)
     * 'notes' ... note text (string|NULL)
     */
    protected function _formatSessionLearningMaterialsFromInput (array $raw)
    {
        $rhett = array();
        foreach ($raw as $item) {
            $material = array();
            $material['dbId'] = array_key_exists('dbId', $item) ? $item['dbId'] : null; // learning_material_id
            $material['meshTerms'] = $item['meshTerms'];
            $material['required'] = ($item['required'] == 'true');
            $material['notesArePubliclyViewable'] = ($item['notesArePubliclyViewable'] == 'true');
            $material['notes'] = null;
            if ((isset($item['notes'])) && (0 < strlen($item['notes']))) {
                $material['notes'] = $item['notes'];
            }
            $rhett[] = $material;
        }
        return $rhett;
    }

    /**
     * Retrieves program objectives by given associated cohorts. The objectives will be grouped by
     * cohorts.
     * Active school association for these cohorts will be indicated.
     * @param array $cohorts a list of cohort ids
     * @return array a list of program objectives, grouped by their associated cohorts
     */
    protected function _getObjectivesForCohorts ($cohorts)
    {
        $rhett = array();
        $activeSchoolId = $this->session->userdata('school_id');
        foreach ($cohorts as $cohortId) {
            $cohort = $this->cohort->getRowForPrimaryKeyId($cohortId);
            $titleAndObjectives = $this->programYear->getObjectivesAndProgramTitle($cohort->program_year_id);
            $title = $titleAndObjectives['program_title'] . ' - ' . $cohort->title;
            $rhett[] = array(
                'cohort_id' => $cohortId,
                'title' => $title,
                'objectives' => $titleAndObjectives['objectives'],
                'is_active_school' => ($activeSchoolId === $titleAndObjectives['school_id']) ? true : false
            );
        }

        return $rhett;
    }

    //
    //
    // TODO getStudentGroupTressForCohorts and addGroupModelForProgram
    //          are in both CM and OM controllers.. the methods have model dependencies i'm not
    //          prepared to introduce into the abstract superclass, nor am i crazy about having
    //          methods in the superclass which require subclasses using them to specify the
    //          dependencies out of class.. seems funky. I suppose we could make another abstract
    //          class intermediary between AIC and CM/OM
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
                $this->addGroupModelForProgram($groupModel, $programRow->program_id, $programRow->title,
                    $cohort->program_year_id, $cohort->title, $rhett);
            }
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     */
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
