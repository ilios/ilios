<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * Curriculum Inventory management controller.
 */
class Curriculum_Inventory_Manager extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Curriculum_Inventory_Program', 'invProgram', true);
        $this->load->model('Curriculum_Inventory_Institution', 'invInstitution', true);
    }

    /**
     * Default action, alias for "view".
     * @see Curriculum_Inventory_Manager::view()
     */
    public function index ()
    {
        $this->view();
    }

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

        $this->load->view('curriculum_inventory/curriculum_inventory_manager', $data);
    }

    /**
     * Creates a new curriculum inventory program for a given program year.
     * Expects the following input in the request parameter string:
     *    'py_id' ... the program year id.
     * On successful creation, the user will be redirected to view the new inventory program.
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

        //
        // create new inventory program for a given ilios program-year
        //
        $programYearId = (int) $this->input->get('py_id');

        // check if ilios program year exists
        $programYear = $this->programYear->getRowForPrimaryKeyId($programYearId, true);
        if (! isset($programYear)) {
            show_error('A program year with the given id does not exist.');
            return;
        }

        // check if a curriculum inventory program already exists
        $invProgram = $this->invProgram->getRowForPrimaryKeyId($programYearId);
        if (isset($invProgram)) {
            show_error('A curriculum inventory report already exists for the given program year.');
            return;
        }

        // load program
        $program = $this->program->getRowForPrimaryKeyId($programYear->program_id, true);

        $startYear = $programYear->start_year;
        $endYear = $startYear + 1;
        $name = $program->title;
        // create default start/end date of program, based on the given program year date
        // @todo make hardwired start/end-date day/month configurable.
        $startDate = new DateTime();
        $startDate->setDate($startYear, 7, 1);
        $endDate = new DateTime();
        $endDate->setDate($endYear, 6, 30);

        // create the new inventory program record
        $created = $this->invProgram->create($programYearId, $name, $startDate, $endDate);
        if (false === $created) {
            show_error('Failed to create curriculum inventory report.');
            return;
        }

        // success! redirect to view the new program
        redirect('curriculum_inventory_manager/view?py_id=' . $programYearId);
    }

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
    }

    public function save ()
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
    }

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
        $progamYearId = (int) $this->input->get('py_id');

        if (0 >= $progamYearId) {
            show_error('Missing or invalid program year id.');
            return;
        }

        try {
            $xml = $this->_getXmlReport($progamYearId);
        } catch (Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error('An error occurred while exporting the curriculum inventory.');
            return;
        }

        $out = $xml->saveXML();
        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            show_error('An error occurred while exporting the curriculum inventory.');

        }
        // all is good, output the XML
        header("Content-Type: application/xml");
        echo $out;
    }

    //
    // XML export functionality
    // @todo move this into a "CodeIgniter library" component
    //

    /**
     * Retrieves the inventory report as XML for a given program year.
     * @param int $programYearId program year id
     * @return boolean|DomDocument the XML report, or FALSE on failure
     * @throws Exception
     */
    protected function _getXmlReport ($programYearId)
    {
        // @todo conditionally, load the xml from file
        return $this->_createXmlReport($programYearId);
    }

    protected function _createXmlReport ($programYearId)
    {
        // @todo load the curriculum inventory
        $inventory = $this->_loadCurriculumInventory($programYearId);
        // @todo
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $rootNode = $dom->createElementNS('http://ns.medbiq.org/curriculuminventory/v1/', 'CurriculumInventory');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $rootNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation',
            'http://ns.medbiq.org/curriculuminventory/v1/curriculuminventory.xsd');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:lom', 'http://ltsc.ieee.org/xsd/LOM');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://ns.medbiq.org/address/v1/');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cf', 'http://ns.medbiq.org/competencyframework/v1/');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:co', 'http://ns.medbiq.org/competencyobject/v1/');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:hx', 'http://ns.medbiq.org/lom/extend/v1/');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:m', 'http://ns.medbiq.org/member/v1/');
        $dom->appendChild($rootNode);
        //
        // ReportID
        //
        $reportIdNode = $dom->createElement('ReportID', $inventory['report']['id']);
        $reportIdNode->setAttribute('domain', $inventory['report']['domain']);
        $rootNode->appendChild($reportIdNode);

        // Institution
        $institutionNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:Institution');
        $rootNode->appendChild($institutionNode);
        $institutionNameNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:InstitutionName');
        $institutionNameNode->appendChild($dom->createTextNode($inventory['institution']->name));
        $institutionNode->appendChild($institutionNameNode);
        $institutionIdNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:InstitutionID', $inventory['institution']->aamc_id);
        $institutionIdNode->setAttribute('domain', 'idd:aamc.org:institution');
        $institutionNode->appendChild($institutionIdNode);
        $addressNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:Address');
        $institutionNode->appendChild($addressNode);
        $streetAddressNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:StreetAddressName');
        $streetAddressNode->appendChild($dom->createTextNode($inventory['institution']->address_street));
        $addressNode->appendChild($streetAddressNode);
        $cityNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:City', $inventory['institution']->address_city);
        $addressNode->appendChild($cityNode);
        $stateNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:StateOrProvince', $inventory['institution']->address_state_or_province);
        $addressNode->appendChild($stateNode);
        $zipcodeNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:PostalCode', $inventory['institution']->address_zipcode);
        $addressNode->appendChild($zipcodeNode);
        $countryNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:Country');
        $addressNode->appendChild($countryNode);
        $countryCodeNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:CountryCode', $inventory['institution']->address_country_code);
        $countryNode->appendChild($countryCodeNode);
        //
        // Program
        //
        $programNode = $dom->createElement('Program');
        $rootNode->appendChild($programNode);
        $programNameNode = $dom->createElement('ProgramName');
        $programNameNode->appendChild($dom->createTextNode($inventory['program']->name));
        $programNode->appendChild($programNameNode);
        $programIdNode = $dom->createElement('ProgramID', $inventory['program']->aamc_id);
        $programIdNode->setAttribute('domain', 'idd:aamc.org:program');
        $programNode->appendChild($programIdNode);

        //
        // various other report attributes
        //
        $titleNode = $dom->createElement('Title');
        $titleNode->appendChild($dom->createTextNode($inventory['program']->name));
        $rootNode->appendChild($titleNode);
        $reportDateNode = $dom->createElement('ReportDate', date('Y-m-d'));
        $rootNode->appendChild($reportDateNode);
        $reportingStartDateNode = $dom->createElement('ReportingStartDate', $inventory['program']->start_date);
        $rootNode->appendChild($reportingStartDateNode);
        $reportingEndDateNode = $dom->createElement('ReportingEndDate', $inventory['program']->end_date);
        $rootNode->appendChild($reportingEndDateNode);
        $languageNode = $dom->createElement('Language', 'en-US'); // @todo make this configurable
        $rootNode->appendChild($languageNode);
        // for now, report title = report description = program title
        // @todo provide means to provide differen values for report title and report description
        $descriptionNode = $dom->createElement('Description');
        $descriptionNode->appendChild($dom->createTextNode($inventory['program']->name));
        $rootNode->appendChild($descriptionNode);
        // default supporting link url to the site url of this Ilios instance.
        // @todo make this configurable
        $supportingLinkNode = $dom->createElement('SupportingLink', base_url());
        $rootNode->appendChild($supportingLinkNode);
        //
        // Events
        //
        return $dom;
    }

    protected function _saveXmlReport ($xml)
    {
        // @todo implement
    }

    /**
     * Retrieves all the entire curriculum inventory for a given program year.
     * @param int $programYearId the program year
     * @return array an associated array, containing the inventory. Data is keyed off by:
     *     'report' ... an array holding various report-related properties, such as id, domain etc
     *     'institution' ... an object representing the curriculum inventory program's owning institution
     *     'program' ... an object representing the curriculum inventory program
     *     'events'
     *     'expectations'
     *     'academic_levels'
     *     'sequence'
     *     'integration'
     * @throws Exception
     * @throws Ilios_Exception
     */
    protected function _loadCurriculumInventory ($programYearId)
    {
        $rhett = array();
        $programYear = $this->programYear->getRowForPrimaryKeyId($programYearId);
        if (! isset($programYear)) {
            throw new Ilios_Exception('Could not load program year for the given id ( ' . $programYearId . ')');
        }

        $invProgram = $this->invProgram->getRowForPrimaryKeyId($programYear->program_year_id);
        if (! isset($invProgram)) {
            throw new Ilios_Exception('Could not load curriculum inventory program for the given id ( ' . $programYearId . ')');
        }

        $program = $this->program->getRowForPrimaryKeyId($programYear->program_id);
        $invInstitution  = $this->invInstitution->getRowForPrimaryKeyId($program->owning_school_id);

        $rhett['report'] = array();
        $rhett['report']['id'] = time();
        $rhett['report']['domain'] = 'idd:curriculum.ucsf.edu:cim';  // @todo change hardwired attribute to reflect ... what exactly?
        $rhett['report']['date'] = date('Y-m-d', $rhett['report']['id']);
        $rhett['program'] = $invProgram;
        $rhett['institution'] = $invInstitution;

        return $rhett;
    }
}