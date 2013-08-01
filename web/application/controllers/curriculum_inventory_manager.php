<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Curriculum Inventory management controller.
 *
 * @see Ilios_CurriculumInventory_Exporter
 */
class Curriculum_Inventory_Manager extends Ilios_Web_Controller
{
    /**
     * The inventory exporter.
     * @var Ilios_CurriculumInventory_Exporter
     */
    protected $_exporter;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        // conditionally load all necessary DAOs
        if (! property_exists($this, 'clerkshipType')) {
            $this->load->model('Course_Clerkship_Type', 'clerkshipType', true);
        }
        if (! property_exists($this, 'inventory')) {
            $this->load->model('Curriculum_Inventory', 'inventory', true);
        }
        if (! property_exists($this, 'invReport')) {
            $this->load->model('Curriculum_Inventory_Report', 'invReport', true);
        }
        if (! property_exists($this, 'invAcademicLevel')) {
            $this->load->model('Curriculum_Inventory_Academic_Level', 'invAcademicLevel', true);
        }
        if (! property_exists($this, 'invInstitution')) {
            $this->load->model('Curriculum_Inventory_Institution', 'invInstitution', true);
        }
        if (! property_exists($this, 'invSequence')) {
            $this->load->model('Curriculum_Inventory_Sequence', 'invSequence', true);
        }
        if (! property_exists($this, 'invSequenceBlock')) {
            $this->load->model('Curriculum_Inventory_Sequence_Block', 'invSequenceBlock', true);
        }

        if (! property_exists($this, 'invExport')) {
            $this->load->model('Curriculum_Inventory_Export', 'invExport', true);
        }

        if (! property_exists($this, 'program')) {
            $this->load->model('Program', 'program', true);
        }

        $this->_exporter = new Ilios_CurriculumInventory_Exporter($this);
    }

    /**
     * Default action.
     *
     * This action prints the curriculum inventory manager for a requested report.
     * If no report is requested, then an bare page will be printed with just the controls to create and search reports.
     *
     * It accepts the following query string parameters:
     *    'report_id' ... (optional) the report id.
     */
    public function index ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($lang, $data);
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if (! isset($schoolRow)) {
            $msg = $this->languagemap->getI18NString('general.error.school_not_found', $lang);
            show_error($msg);
            return;
        }

        $payload = array(); // data container

        $programs = $this->program->getAllPublishedProgramsWithSchoolId($schoolId);
        $payload['programs'] = $programs;

        $reports = $this->invReport->getList($schoolId); // existing reports
        $data['reports'] = $reports;

        $reportId = $this->input->get('report_id');

        if ($reportId) {
            $report = $this->invReport->getRowForPrimaryKeyId($reportId);
            if (! $report) {
                $msg = $this->languagemap->getI18NString('curriculum_inventory.report.load.general_error', $lang);
                show_error($msg);
                return;
            }
            $program = $this->program->getRowForPrimaryKeyId($report->program_id);
            $report->is_finalized = $this->invExport->exists($reportId);
            $report->program = $program;
            $academicLevels = $this->invAcademicLevel->getLevels($report->report_id);
            $linkedCourses = $this->inventory->getLinkedCourses($report->report_id);
            $linkableCourses = $this->inventory->getLinkableCourses($report->year, $schoolId, $report->report_id);
            $courses = array_merge($linkedCourses, $linkableCourses);
            $sequenceBlocks = $this->invSequenceBlock->getBlocks($report->report_id);
            $sequenceBlocks = $this->invSequenceBlock->buildSequenceBlockHierarchy($sequenceBlocks);

            $payload['report'] = $report;
            $payload['academic_levels'] = $academicLevels;
            $payload['courses'] = $courses;
            $payload['sequence_blocks'] = $sequenceBlocks;
        }

        $data['payload'] = Ilios_Json::encodeForJavascriptEmbedding($payload, Ilios_Json::JSON_ENC_SINGLE_QUOTES);
        $this->load->view('curriculum_inventory/index', $data);
    }

    /**
     * This action creates a new curriculum inventory report for a given academic year and program.
     *
     * It expects the following POST parameters:
     *    'report_year' ... the academic year
     *    'report_name' ... the report name
     *    'report_description' ... the report description
     *    'program_id' ... the program id
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "report_id" which contains the id of the newly created report.
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function create ()
    {
        $lang = $this->getLangToUse();
        $rhett = array();

        $data = array();
        $data['lang'] = $lang;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $year = (int) $this->input->post('report_year');
        $reportName = $this->input->post('report_name');
        $reportDescription = $this->input->post('report_description');
        $programId = (int) $this->input->post('program_id');

        //
        // create new inventory report for the given academic year
        //

        // check if a curriculum inventory report already exists
        $invReport = $this->invReport->getByAcademicYearAndProgram($year, $programId);
        if (isset($invReport)) {
            $this->_printErrorXhrResponse('curriculum_inventory.create.error.already_exists', $lang);
            return;
        }

        $startYear = $year;
        $endYear = $startYear + 1;
        // create start/end date for the report
        // @todo make hardwired start/end day/month configurable
        $startDate = new DateTime();
        $startDate->setDate($startYear, 7, 1);
        $endDate = new DateTime();
        $endDate->setDate($endYear, 6, 30);

        // create a new curriculum inventory report and associated entities (academic levels, sequence etc.)
        // @todo running db transactions in the controller - BAD! refactor this out.
        $this->db->trans_start();
        $reportId = $this->invReport->create($year, $programId, $reportName, $reportDescription, $startDate, $endDate);
        $this->invAcademicLevel->createDefaultLevels($reportId);
        $this->invSequence->create($reportId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.create.error.general', $lang);
            return;
        }

        $rhett['report_id'] = $reportId;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action updates an existing report from user input.
     *
     * It expects the following POST parameters:
     *    'report_id' ... the report id
     *    'report_name' ... the report name
     *    'report_description' ... the report description
     *
     * After completing the update, this method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "report" which contains the updated report record.
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function update ()
    {
        $lang = $this->getLangToUse();
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $reportId = (int) $this->input->post('report_id');
        $reportName = $this->input->post('report_name');
        $reportDescription = $this->input->post('report_description');
        $startDate = date_create($this->input->post('start_date'));
        $endDate = date_create($this->input->post('end_date'));

        // check if a curriculum inventory report already exists
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_does_not_exist', $lang);
            return;
        }

        // input validation
        if ('' === trim($reportName)) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_name_missing', $lang);
            return;
        }
        if ('' === trim($reportDescription)) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_description_missing', $lang);
            return;
        }
        if (false === $startDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.invalid_start_date', $lang);
            return;
        }
        if (false === $endDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.invalid_end_date', $lang);
            return;
        }

        // updates the report record
        $this->db->trans_start();
        $this->invReport->update($reportId, $reportName, $reportDescription, $startDate, $endDate);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.general', $lang);
            return;
        }

        // reload update report
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        $program = $this->program->getRowForPrimaryKeyId($invReport->program_id);
        $invReport->program = $program;

        $rhett['report'] = $invReport;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action generates and prints a requested curriculum inventory report as XML document.
     *
     * It expects the following POST parameters:
     *    'report_id' ... the report id
     *    'download_token' ... the download token
     *
     * On success, it set a cookie containing the given download token and then prints out the requested report document.
     * On failure, an error page will be printed.
     */
    public function export ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($lang, $data);
            return;
        }

        // input validation
        $reportId = (int) $this->input->get('report_id');
        $downloadToken = filter_var($this->input->get('download_token'), FILTER_SANITIZE_NUMBER_INT);
        if (0 >= $reportId) {
            show_error($this->languagemap->getI18NString('curriculum_inventory.validate.error.report_id_missing', $lang));
            return;
        }

        // generate and export the report to XML
        try {
            $xml = $this->_exporter->getXmlReport($reportId);
        } catch (DomException $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.generate', $lang));
            return;
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.generate', $lang));
            return;
        }

        $out = $xml->saveXML();

        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.xml', $lang));
            return;
        }

        // set the cookie containing the download token
        $this->input->set_cookie('download-token', $downloadToken, 0);

        // all is good, output the XML
        header('Content-Type: application/xml; charset="utf8"');
        header('Content-disposition: attachment; filename="report.xml"');
        echo $out;
    }

    /**
     * This action deletes a requested report and all of it's associated inventory-specific data points.
     *
     * It expects the following POST parameters::
     *    'report_id' ... the report id
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "success", which contains the value "true".
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function delete ()
    {
        $lang = $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        // input validation
        $reportId = (int) $this->input->post('report_id');
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_does_not_exist', $lang);
            return;
        }
        // delete the report and associated records
        $this->db->trans_start();
        $this->invReport->delete($reportId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.delete.error.general', $lang);
            return;
        }
        $rhett = array('success' => 'true');
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action retrieves and prints a finalized report document from the database.
     *
     * It expects the following POST parameters:
     *    'report_id' ... the report id
     *    'download_token' ... the download token
     *
     * On success, it set a cookie containing the given download token and then prints out the requested report document.
     * On failure, an error page will be printed.
     */
    public function download ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['institution_name'] = $this->config->item('ilios_institution_name');
        $data['user_id'] = $this->session->userdata('uid');

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($lang, $data);
            return;
        }

        // input validation
        $reportId = (int) $this->input->get('report_id');
        $downloadToken = filter_var($this->input->get('download_token'), FILTER_SANITIZE_NUMBER_INT);
        if (0 >= $reportId) {
            show_error($this->languagemap->getI18NString('curriculum_inventory.validate.error.report_id_missing', $lang));
            return;
        }

        // retrieve the report from the db and print it.
        $report = $this->invExport->getRowForPrimaryKeyId($reportId);

        if (! $report) {
            log_message('error', 'CIM export: No finalized report was found with the given id.');
            show_error($this->languagemap->getI18NString('curriculum_inventory.download.error.export_not_found', $lang));
            return;
        }


        // set the cookie containing the download token
        $this->input->set_cookie('download-token', $downloadToken, 0);

        // all is good, output the XML
        header('Content-Type: application/xml; charset="utf8"');
        header('Content-disposition: attachment; filename="report.xml"');

        echo $report->document;
    }

    /**
     * This action generates an inventory report, stores it in the database and flags the associated report record
     * as "finalized".
     *
     * It expects the following POST parameters::
     *    'report_id' ... the report id
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "success", which contains the value "true".
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function finalize ()
    {
        $lang = $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $userId = $this->session->userdata('uid');

        $reportId = (int) $this->input->post('report_id');

        // check if a curriculum inventory report already exists
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_does_not_exist', $lang);
            return;
        }

        // check if the report has already been finalized
        if ($this->invExport->exists($reportId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.finalize.error.already_finalized', $lang);
            return;
        }

        // generate the XML report
        try {
            $xml = $this->_exporter->getXmlReport($reportId);
        } catch (DomException $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.generate', $lang);
            return;
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.generate', $lang);
            return;
        }

        $out = $xml->saveXML();
        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.xml', $lang);
            return;
        }
        // save the export to the db
        if (! $this->invExport->create($reportId, $out , $userId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.finalize.error.save', $lang);
            return;
        }
        $rhett = array('success' => 'true');
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * @todo add code docs
     */
    public function createSequenceBlock ()
    {
        // @todo implement
    }

    /**
     * @todo add code docs
     */
    public function updateSequenceBlock ()
    {
        // @todo implement
    }

    /**
     * This action deletes a requested sequence block and all its children.
     *
     * It expects the following POST parameters::
     *    'sequence_block_id' ... the report id
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "success", which contains the value "true".
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function deleteSequenceBlock ()
    {
        $lang = $this->getLangToUse();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        // input validation
        $sequenceBlockId = (int) $this->input->post('sequence_block_id');
        $invReport = $this->invSequenceBlock->getRowForPrimaryKeyId($sequenceBlockId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.update.error.does_not_exist', $lang);
            return;
        }
        // delete the report and associated records
        $this->db->trans_start();
        $this->invSequenceBlock->delete($sequenceBlockId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.delete.error.general', $lang);
            return;
        }
        $rhett = array('success' => 'true');
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }
}
