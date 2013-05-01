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

        if (! property_exists($this, 'program')) {
            $this->load->model('Program', 'program', true);
        }

        $this->_exporter = new Ilios_CurriculumInventory_Exporter($this);
    }

    /**
     * Default action.
     *
     * It prints a page with dialogs for searching for and creating new reports.
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
            show_error('Failed to load school data for this user session.');
            return;
        }

        $programs = $this->program->getAllPublishedProgramsWithSchoolId($schoolId);

        $data['programs'] = Ilios_Json::encodeForJavascriptEmbedding($programs, Ilios_Json::JSON_ENC_SINGLE_QUOTES);

        $this->load->view('curriculum_inventory/index', $data);
    }

    /**
     * This action prints the curriculum inventory manager for a requested report.
     *
     * It accepts the following query string parameters:
     *    'report_id' ... the report id.
     */
    public function view ()
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
            show_error('Failed to load school data for this user session.');
            return;
        }

        $data['school_id'] = $schoolId;
        $data['school_name'] = $schoolRow->title;
        $key = 'general.phrases.school_of';
        $schoolOfStr = $this->languagemap->getI18NString($key, $lang);
        $data['viewbar_title'] = $data['institution_name'] . ' - ' . $schoolOfStr . ' ' . $schoolRow->title;

        //
        // load curriculum inventory program for the given program year
        //

        // @todo

        $this->load->view('curriculum_inventory/view', $data);
    }

    /**
     * This action creates a new curriculum inventory report for a given academic year and program.
     *
     * It accepts the following POST parameters:
     *    'year' ... the academic year
     *    'name' ... the report name
     *    'description' ... the report description
     *    'program_id' ... the program id
     *
     * On successful creation, the user will be redirected to view the new report.
     * Any failure will cause in an error page being rendered.
     */
    public function create ()
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
            show_error('Failed to load school data for this user session.');
            return;
        }

        $year = (int) $this->input->post('year');
        $reportName = $this->input->post('name');
        $reportDescription = $this->input->post('description');
        $programId = (int) $this->input->post('program_id');
        //
        // create new inventory report for the given academic year
        //

        // check if a curriculum inventory report already exists
        $invReport = $this->invReport->getByAcademicYearAndProgram($year, $programId);
        if (isset($invReport)) {
            show_error('A curriculum inventory report already exists for the given academic year and program.');
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
        $reportId = $this->invReport->create($year, $schoolId, $reportName, $reportDescription, $startDate, $endDate);
        $this->invAcademicLevel->createDefaultLevels($reportId);
        $this->invSequence->create($reportId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            show_error('Failed to create curriculum inventory report.');
            return;
        }

        // success! redirect to view the new program
        redirect('curriculum_inventory_manager/view?report_id=' . $reportId);
    }

    /**
     * This action prints a requested curriculum inventory report as HTML document.
     *
     * It accepts the following query string parameters:
     *    'report_id' ... the report id.
     */
    public function preview ()
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
        if (0 >= $reportId) {
            show_error('Missing or invalid report id.');
            return;
        }

        try {
            $inventory = $this->_loadCurriculumInventory($reportId);
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error('An error occurred while loading the curriculum inventory.');
            return;
        }

        $data['inventory'] = $inventory;
        $this->load->view('curriculum_inventory/preview', $data);
    }

    /**
     * This action exports a requested curriculum inventory report as XML document.
     *
     * It accepts the following query string parameters:
     *    'report_id' ... the report id
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
        if (0 >= $reportId) {
            show_error('Missing or invalid report id.');
            return;
        }

        // @todo conditionally load the "finalized" report from the database

        // generate and export the report to XML
        try {
            $xml = $this->_exporter->getXmlReport($reportId);

        } catch (DomException $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error('An error occurred while exporting the curriculum inventory report to XML.');
            return;
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error('An error occurred while loading the curriculum inventory.');
            return;
        }

        $out = $xml->saveXML();
        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            show_error('An error occurred while exporting the curriculum inventory report.');
        }

        // all is good, output the XML
        header('Content-Type: application/xml; charset="utf8"');
        header('Content-disposition: attachment; filename="report.xml"');
        echo $out;
    }

    /**
     * This action searches reports in the currently active school by a given search term.
     *
     * It accepts the following POST parameters:
     *   "report_search_term" ... the search term to use.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "reports" which contains an array of inventory reports.
     * If no reports were found for the given search term, then this array is empty.
     *
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function searchReports ()
    {
        $lang =  $this->getLangToUse();
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse($lang);
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if (! isset($schoolRow)) {
            $this->_printErrorXhrResponse('Failed to load school data for this user session.', $lang);
            return;
        }

        $term = trim($this->input->post('report_search_term'));

        $rhett['reports'] = $this->invReport->search($schoolId, $term);

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }
}
