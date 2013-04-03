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

        // create the new inventory program record
        $created = $this->invProgram->create($programYearId);
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
            log_message('error',  'CIM export: ' . $e->getMessage);
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
     *     'institution'
     *     'program'
     *     'events'
     *     'expectations'
     *     'academic_levels'
     *     'sequence'
     *     'integration'
     *
     * @throws Exception
     */
    protected function _loadCurriculumInventory ($programYearId)
    {
        return array();
    }

}