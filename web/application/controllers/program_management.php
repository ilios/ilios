<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Program Management Controller.
 */
class Program_Management extends Ilios_Web_Controller
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Program', 'program', true);
        $this->load->model('Publish_Event', 'publishEvent', true);
        $this->load->model('School', 'school', true);
    }

    /**
     * Default controller action.
     * Loads and populates the program manager view.
     */
    public function index ()
    {
        $data = array();

        if (! $this->session->userdata('has_instructor_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $this->output->set_header('Expires: 0');

        $programId = $this->input->get_post('program_id');

        $schoolId =  $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        $data['viewbar_title'] = $this->config->item('ilios_institution_name');

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

        if ($programId != '') {
            $data['program_row'] = $this->convertStdObjToArray($this->program->getRowForPrimaryKeyId($programId));
            $data['disabled'] = false;
        } else {
            $dummyRow = array();
            $dummyRow['program_id'] = '';
            $dummyRow['title'] = '';
            $dummyRow['short_title'] = '';
            $dummyRow['duration'] = '';
            $dummyRow['publish_event_id'] = '';

            $data['program_row'] = $dummyRow;
            $data['disabled'] = true;
        }

        // get school competencies
        $schoolCompetencies = $this->_getSchoolCompetencies();
        $data['school_competencies'] = Ilios_Json::encodeForJavascriptEmbedding($schoolCompetencies,
            Ilios_Json::JSON_ENC_SINGLE_QUOTES);

        $key = 'program_management.title_bar';
        $data['title_bar_string'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.search.title';
        $data['program_search_title'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.select_associated_competency';
        $data['select_competency'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.page_header';
        $data['page_header_string'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.objective_edit_title';
        $data['edit_objective_dialog_title'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.duration.in_years';
        $data['duration_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.collapse_all';
        $data['collapse_program_years_string'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.add_program';
        $data['add_program_string'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.add_new_program';
        $data['add_new_program_string'] = $this->languagemap->getI18NString($key);

        $key = 'program_management.add_program_year';
        $data['add_program_year_string'] = $this->languagemap->getI18NString($key);

        $key = 'mesh.dialog.search_mesh';
        $data['mesh_search_mesh'] = $this->languagemap->getI18NString($key);

        $key = 'mesh.dialog.title';
        $data['mesh_dialog_title'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.program_title_full';
        $data['program_title_full_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.program_title_short';
        $data['program_title_short_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.search.clear';
        $data['generic_search_clear'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.search.hint';
        $data['generic_search_hint'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.help';
        $data['word_help_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.terms.search';
        $data['word_search_string'] = $this->languagemap->getI18NString($key);

        $key = 'general.phrases.show_less';
        $data['phrase_show_less_string'] = strtolower($this->languagemap->getI18NString($key));

        $key = 'general.phrases.show_more';
        $data['phrase_show_more_string'] = strtolower($this->languagemap->getI18NString($key));

        $data['user_preferences_json'] = json_encode($this->_getUserPreferences());

        $this->load->view('program/program_manager', $data);
    }

    /**
     * XHR handler.
     * Searches for a given (partial) program title.
     * Prints out a JSON-formatted list of matching programs.
     * Expects the following values to be POSTed:
     * - 'query' ... a title/title-fragment to search programs by
     */
    public function getProgramListForQuery ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $title = $this->input->get_post('query');
        $schoolId = $this->session->userdata('school_id');
        $uid = $this->session->userdata('uid');
        $queryResults = $this->program->getProgramsFilteredOnTitleMatch($title, $schoolId, $uid);

        $rhett = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Prints out a JSON-formatted list of programs-years
     * associated with a given program id.
     */
    public function getProgramYears ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $programId = $this->input->get_post('program_id');
        $row = $this->program->getRowForPrimaryKeyId($programId);
        $schoolOwnsProgram = ($this->session->userdata('school_id') == $row->owning_school_id);
        $yearArray = $this->programYear->getProgramYearsForProgram($programId);

        $rhett = array();
        $rhett['school_owns_program'] = $schoolOwnsProgram ? 'true' : false;
        $rhett['years'] = $yearArray;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     *
     * This takes no arguments presently and returns a tree of all non-deleted schools and
     * departments in the database.
     *
     * Prints out a JSON'd non-associative array of school objects, each object being an
     * associative array with keys 'school_id', 'title', and 'departments'.
     * The value for the 'departments' key is a non-associative array of department objects,
     * each object being an associative array with keys 'department_id' and 'title'.
     * Schools or departments which have their deleted bit set will not be returned.
     */
    public function getSchoolTree ()
    {
        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        header("Content-Type: text/plain");
        echo json_encode($this->school->getSchoolTree());
    }

    /**
     * XHR handler.
     *
     * Called from the program main entity container via AJAX.
     *
     * Echos out a JSON'd map;
     * on failure cases it will contain one entry with the key being 'error';
     * on success cases - it will contain 5 entires with the keys of 'pid',
     * 'title', 'short_title', 'duration', and 'publish'.
     */
    public function saveProgram ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        // TODO this is one of the few places we do server side validation.. this meme was
        // abandoned early on and so this code should probably go for the sake
        // of uniformity
        $this->load->library('form_validation');

        $this->form_validation->set_rules('program_title', 'Program Title (Full)', 'trim|required');
        $this->form_validation->set_rules('short_title', 'Program Title (Short)', 'trim|required|max_length[10]');

        if (! $this->form_validation->run()) {
            $msg = $this->languagemap->getI18NString('general.error.data_validation');

            $rhett['error'] = $msg . ": " . validation_errors();
        } else {
            $title = rawurldecode($this->input->get_post('program_title'));
            $short = rawurldecode($this->input->get_post('short_title'));
            $duration = $this->input->get_post('duration');
            $programId = $this->input->get_post('program_id');

            $publish = $this->input->get_post('publish');

            $failedTransaction = true;
            $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
            do {
                $auditAtoms = array();

                unset($rhett['error']);
                $publishId = - 1;

                $this->program->startTransaction();

                $failed = false;

                if ($publish == "true") {
                    $publishId = $this->publishEvent->addPublishEvent("program", $programId,
                        $this->getClientIPAddress(), $userId, $auditAtoms);

                    $failed = $this->publishEvent->transactionAtomFailed();
                }

                if (! $failed) {
                    $this->program->updateProgramWithId($programId, $title, $short, $duration, $publishId, $auditAtoms);
                }

                if ($failed || $this->program->transactionAtomFailed()) {
                    $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->program);
                } else {
                    $this->program->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    $failedTransaction = false;

                    $rhett['pid'] = $programId;
                    $rhett['duration'] = $duration;
                    $rhett['title'] = $title;
                    $rhett['short_title'] = $short;
                    $rhett['publish'] = $publishId;
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Called from the program add dialog on the program_manager.php generated page via AJAX.
     *
     * Echos out a JSON'd map;
     * on failure cases it will contain one entry with the key being 'error';
     * on success cases - the map will have 4 entries of keys 'pid', 'title',
     * 'short', and 'duration'.
     */
    public function addNewProgram ()
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
        $this->form_validation->set_rules('new_program_title', 'Program Title (Full)', 'trim|required');
        $this->form_validation->set_rules('new_short_title', 'Program Title (Short)', 'trim|required|max_length[10]');

        $title = $this->input->get_post('new_program_title');
        $short = $this->input->get_post('new_short_title');

        if (! $this->form_validation->run()) {
            $msg = $this->languagemap->getI18NString('general.error.data_validation');

            $rhett['error'] = $msg . ": " . validation_errors();
        } else {
            $duration = $this->input->get_post('duration');

            $failedTransaction = true;
            $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
            do {
                $auditAtoms = array();

                unset($rhett['error']);

                $this->program->startTransaction();

                $newId = $this->program->addNewProgram($title, $short, $duration, $schoolId, $auditAtoms);

                if (($newId <= 0) || $this->program->transactionAtomFailed()) {
                    $msg = $this->languagemap->getI18NString('general.error.db_insert');

                    $rhett['error'] = $msg;

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->program);
                } else {
                    $this->program->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    $failedTransaction = false;

                    $rhett['pid'] = $newId;
                    $rhett['duration'] = $duration;
                    $rhett['title'] = $title;
                    $rhett['short_title'] = $short;
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Called from a program year entity container via AJAX.
     *
     * Echos out a JSON'd map;
     * on failure cases it will contain 2 entries with the keys being 'error' and 'container';
     * on success cases - it will contain 2 entries with the keys being 'success' and 'container'.
     * 'container' is a passback of what's been    passed in as 'cnumber'
     */
    public function deleteProgramYear ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $programYearId = $this->input->get_post('program_year_id');

        $containerNumber = $this->input->get_post('cnumber');
        $rhett['container'] = $containerNumber;

        if ((! isset($programYearId)) || ($programYearId == '')) {
            $msg = $this->languagemap->getI18NString('general.error.data_validation');

            $rhett['error'] = $msg;
        } else {
            $failedTransaction = true;
            $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
            do {
                $auditAtoms = array();

                unset($rhett['error']);

                $this->programYear->startTransaction();

                if ($this->programYear->deleteProgramYear($programYearId, $auditAtoms)) {
                    $this->programYear->commitTransaction();

                    // save audit trail
                    $this->auditAtom->startTransaction();
                    $success = $this->auditAtom->saveAuditEvent($auditAtoms, $userId);
                    if ($this->auditAtom->transactionAtomFailed() || ! $success) {
                        $this->auditAtom->rollbackTransaction();
                    } else {
                        $this->auditAtom->commitTransaction();
                    }

                    $failedTransaction = false;

                    $rhett['success'] = "ya";
                } else {
                    $msg = $this->languagemap->getI18NString('general.error.db_delete');

                    $rhett['error'] = $msg;

                    Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->programYear);
                }
            } while ($failedTransaction && ($transactionRetryCount > 0));
        }

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * XHR handler.
     * Locks (and optionally archives) a given program year.
     *
     * Prints out an JSON-formatted array containing 'success' on success,
     * otherwise 'error' with an error msg. on failure.
     */
    public function lockProgramYear ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_instructor_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $programYearId = $this->input->get_post('program_year_id');
        $archiveAlso = ($this->input->get_post('archive') == 'true');

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);

            $this->programYear->startTransaction();

            $this->programYear->lockOrArchiveProgramYear($programYearId, true, $archiveAlso, $auditAtoms);
            if ($this->programYear->transactionAtomFailed()) {
                $rhett['error'] = $this->languagemap->getI18NString('general.error.db_insert');

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->programYear);
            } else {
                $this->programYear->commitTransaction();

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

    /**
     * XHR handler.
     * Called from a program year entity container via AJAX.
     *
     * Echos out a JSON'd map; on failure cases it will contain 2 entries with the keys being
     * 'error' and 'container'; on success cases - it will contain 4 entires 'pyid',
     * 'start_year', 'publish', and 'container'. 'container' is a passback of what's
     * been passed in as 'cnumber'
     */
    public function saveProgramYear ()
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
        // input validation and sanitation
        //
        try {
            $competencies = Ilios_Json::deserializeJsonArray($this->input->post('competency'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('program_management.error.program_save.input_validation.competencies');
            return;
        }
        try {
            $directors = Ilios_Json::deserializeJsonArray($this->input->post('director'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('program_management.error.program_save.input_validation.directors');
            return;
        }
        try {
            $disciplines = Ilios_Json::deserializeJsonArray($this->input->post('discipline'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('program_management.error.program_save.input_validation.disciplines');
            return;
        }
        try {
            $objectives = Ilios_Json::deserializeJsonArray($this->input->post('objective'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('program_management.error.program_save.input_validation.objectives');
            return;
        }
        try {
            $stewards = Ilios_Json::deserializeJsonArray($this->input->post('steward'), true);
        } catch (Ilios_Exception $e) {
            $this->_printErrorXhrResponse('program_management.error.program_save.input_validation.stewards');
            return;
        }

        $containerNumber = $this->input->post('cnumber');
        $rhett['container'] = $containerNumber;
        $startYear = $this->input->post('start_year');
        $programYearId = $this->input->post('program_year_id');
        $programId = $this->input->post('program_id');
        $publish = $this->input->post('publish');

        $publishNeedsUpdating = false;
        $newProgramYear = ($programYearId == - 1);

        $failedTransaction = true;
        $transactionRetryCount = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT;
        do {
            $auditAtoms = array();

            unset($rhett['error']);
            $publishId = - 1;

            $this->programYear->startTransaction();

            $failed = false;

            if ($publish == "true") {
                $publishNeedsUpdating = $newProgramYear;

                $publishId = $this->publishEvent->addPublishEvent("program_year", $programYearId,
                    $this->getClientIPAddress(), $userId, $auditAtoms);

                $failed = $this->publishEvent->transactionAtomFailed();
            }

            $returningObjectives = array();

            if ($newProgramYear && (! $failed)) {
                $programYearId = $this->programYear->addProgramYear($startYear, $competencies, $objectives, $disciplines, $directors, $stewards, $programId, (($publishId == - 1) ? null : $publishId), $auditAtoms, $returningObjectives);
            } else if (! $failed) {
                $returningObjectives = $this->programYear->updateProgramYearWithId($programYearId, $startYear, $competencies, $objectives, $disciplines, $directors, $stewards, $publishId, $programId, $auditAtoms);
            }

            if (! $failed) {
                $failed = $this->programYear->transactionAtomFailed();
            }

            if ($publishNeedsUpdating && (! $failed)) {
                $this->publishEvent->updatePublishEventTableRowIdColumn($publishId, $programYearId);

                $failed = $this->publishEvent->transactionAtomFailed();
            }
            if ($failed) {
                $rhett['error'] = 'There was a Database Deadlock error.';

                Ilios_Database_TransactionHelper::failTransaction($transactionRetryCount, $failedTransaction, $this->programYear);
            } else {
                $rhett['pyid'] = $programYearId;
                $rhett['start_year'] = $startYear;
                $rhett['publish'] = $publishId;
                $rhett['objectives'] = $returningObjectives;

                $failedTransaction = false;

                $this->programYear->commitTransaction();

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
}
