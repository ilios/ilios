<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * Curriculum Inventory management controller.
 *
 * @todo hideously fat controller, move business logic to helper/workflow/whatever components.
 */
class Curriculum_Inventory_Manager extends Ilios_Web_Controller
{
    /**
     * @var CurriculumInventoryExporter
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
        $this->load->library('CurriculumInventoryExporter');
        // shorthand variable, this makes it easier to work with
        $this->_exporter = $this->curriculuminventoryexporter;
    }

    /**
     * Default action.
     * Lists existing reports for the currently active school.
     */
    public function index ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->languagemap;
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

        $this->load->view('curriculum_inventory/index', $data);
    }

    /**
     * Prints the curriculum inventory manager for a requested report.
     * Expects the following input in the request parameter string:
     *    'report_id' ... the report id.
     */
    public function view ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->languagemap;
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
     * Creates a new curriculum inventory report for a given academic year and program.
     * Expects the following input to be POSTed:
     *    'year' ... the academic year
     *    'name' ... the report name
     *    'description' ... the report description
     *    'program_id' ... the program id
     * On successful creation, the user will be redirected to view the new report.
     * Any failure will cause an error page to be rendered.
     */
    public function add ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->languagemap;
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
     * Prints a requested curriculum inventory report as HTML document.
     * Expects the following input in the request parameter string:
     *    'report_id' ... the report id.
     */
    public function preview ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->languagemap;
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
     * Prints a requested curriculum inventory report as XML document.
     * Expects the following input in the request parameter string:
     *    'report_id' ... the report id
     */
    public function export ()
    {
        $lang = $this->getLangToUse();

        $data = array();
        $data['lang'] = $lang;
        $data['i18n'] =  $this->languagemap;
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
            $xml = $this->_exporter->createXmlReport($reportId);

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


}
