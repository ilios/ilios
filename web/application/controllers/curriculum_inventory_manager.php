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
        $data = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        $schoolId = $this->session->userdata('school_id');
        $schoolRow = $this->school->getRowForPrimaryKeyId($schoolId);

        if (! isset($schoolRow)) {
            $msg = $this->languagemap->getI18NString('general.error.school_not_found');
            show_error($msg);
            return;
        }

        $payload = array(); // data container
        $academicLevels = array();

        $programs = $this->program->getAllPublishedProgramsWithSchoolId($schoolId);
        $payload['programs'] = $programs;

        $reports = $this->invReport->getList($schoolId); // existing reports
        $data['reports'] = $reports;

        $reportId = $this->input->get('report_id');

        if ($reportId) {
            $report = $this->invReport->getRowForPrimaryKeyId($reportId);
            if (! $report) {
                $msg = $this->languagemap->getI18NString('curriculum_inventory.report.load.general_error');
                show_error($msg);
                return;
            }
            $program = $this->program->getRowForPrimaryKeyId($report->program_id);
            $report->is_finalized = $this->invExport->exists($reportId);
            $report->program = $program;
            $academicLevels = array_values($this->invAcademicLevel->getLevels($report->report_id));
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
        // JSONifiy the entire report data array and push it to the view
        $data['payload'] = Ilios_Json::encodeForJavascriptEmbedding($payload, Ilios_Json::JSON_ENC_SINGLE_QUOTES);
        // push the academic levels separately to simplify the "add sequence block" dialog's form population.
        $data['academic_levels'] = $academicLevels;

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
        
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
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
            $this->_printErrorXhrResponse('curriculum_inventory.create.error.already_exists');
            return;
        }

        $startYear = $year;
        $endYear = $startYear + 1;
        // create start/end date for the report
        // @todo replace hardwired start/end day/month with values configured for academic years once issue #565 has been resolved.
        $startDate = new DateTime();
        $startDate->setDate($startYear, 7, 1);
        $endDate = new DateTime();
        $endDate->setDate($endYear, 6, 30);

        // create a new curriculum inventory report and associated entities (academic levels, sequence etc.)
        $this->db->trans_start();
        $reportId = $this->invReport->create($year, $programId, $reportName, $reportDescription, $startDate, $endDate);
        $this->invAcademicLevel->createDefaultLevels($reportId);
        $this->invSequence->create($reportId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.create.error.general');
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
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
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
            $this->_printErrorXhrResponse('curriculum_inventory.validate.error.report_does_not_exist');
            return;
        }

        // reject requests for modifying finalized reports
        if ($this->invExport->exists($reportId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.error.cannot_modify_finalized_report');
            return;
        }

        // input validation
        if ('' === trim($reportName)) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_name_missing');
            return;
        }
        if ('' === trim($reportDescription)) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.report_description_missing');
            return;
        }
        if (false === $startDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.invalid_start_date');
            return;
        }
        if (false === $endDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.invalid_end_date');
            return;
        }

        // updates the report record
        $this->db->trans_start();
        $this->invReport->update($reportId, $reportName, $reportDescription, $startDate, $endDate);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.update.error.general');
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
        $data = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        // input validation
        $reportId = (int) $this->input->get('report_id');
        $downloadToken = filter_var($this->input->get('download_token'), FILTER_SANITIZE_NUMBER_INT);
        if (0 >= $reportId) {
            show_error($this->languagemap->getI18NString('curriculum_inventory.validate.error.report_id_missing'));
            return;
        }

        // generate and export the report to XML
        try {
            $xml = $this->_exporter->getXmlReport($reportId);
        } catch (DomException $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.generate'));
            return;
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.generate'));
            return;
        }

        $out = $xml->saveXML();

        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            show_error($this->languagemap->getI18NString('curriculum_inventory.export.error.xml'));
            return;
        }

        // set the cookie containing the download token
        $this->input->set_cookie('download-token', $downloadToken, 0);

        // all is good, output the XML
        header('Content-Type: application/xml; charset="utf-8"');
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
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        // input validation
        $reportId = (int) $this->input->post('report_id');
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.validate.error.report_does_not_exist');
            return;
        }

        // reject requests for modifying finalized reports
        if ($this->invExport->exists($reportId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.error.cannot_modify_finalized_report');
            return;
        }

        // delete the report and associated records
        $this->db->trans_start();
        $this->invReport->delete($reportId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.delete.error.general');
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
        $data = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_viewAccessForbiddenPage($data);
            return;
        }

        // input validation
        $reportId = (int) $this->input->get('report_id');
        $downloadToken = filter_var($this->input->get('download_token'), FILTER_SANITIZE_NUMBER_INT);
        if (0 >= $reportId) {
            show_error($this->languagemap->getI18NString('curriculum_inventory.validate.error.report_id_missing'));
            return;
        }

        // retrieve the report from the db and print it.
        $report = $this->invExport->getRowForPrimaryKeyId($reportId);

        if (! $report) {
            log_message('error', 'CIM export: No finalized report was found with the given id.');
            show_error($this->languagemap->getI18NString('curriculum_inventory.download.error.export_not_found'));
            return;
        }


        // set the cookie containing the download token
        $this->input->set_cookie('download-token', $downloadToken, 0);

        // all is good, output the XML
        header('Content-Type: application/xml; charset="utf-8"');
        header('Content-disposition: attachment; filename="report.xml"');

        echo $report->document;
    }

    /**
     * This action generates an inventory report, stores it in the database and flags the associated report record
     * as "finalized".
     *
     * It expects the following POST parameters:
     *    'report_id' ... the report id
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains a property "success", which contains the value "true".
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function finalize ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $userId = $this->session->userdata('uid');

        $reportId = (int) $this->input->post('report_id');

        // check if a curriculum inventory report already exists
        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.validate.error.report_does_not_exist');
            return;
        }

        // check if the report has already been finalized
        if ($this->invExport->exists($reportId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.finalize.error.already_finalized');
            return;
        }

        // generate the XML report
        try {
            $xml = $this->_exporter->getXmlReport($reportId);
        } catch (DomException $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.generate');
            return;
        } catch (Ilios_Exception $e) {
            log_message('error',  'CIM export: ' . $e->getMessage());
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.generate');
            return;
        }

        $out = $xml->saveXML();
        if (false === $out) {
            log_message('error', 'CIM export: Failed to convert XML to its String representation.');
            $this->_printErrorXhrResponse('curriculum_inventory.export.error.xml');
            return;
        }
        // save the export to the db
        if (! $this->invExport->create($reportId, $out , $userId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.finalize.error.save');
            return;
        }
        $rhett = array('success' => 'true');
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action creates a new sequence block in the report.
     *
     * It expects the following POST parameters:
     *    'report_id' ... The report id.
     *    'parent_sequence_block_id' ... The id of the block that will be the parent to the newly created sequence block, or '0'
     *          if the newly created block is a top-level block.
     *    'title' ... The title of the block.
     *    'description' ... A description of the block.
     *    'required' ... Indicates whether the block is required ("1"), optional ("2") or required in track ("3").
     *    'minimum' ... Indicates the number of child sequence blocks that a learner must take.
     *    'maximum' ... Indicates the number of child sequence blocks that a learner can take.
     *    'track' ... Indicates whether this sequence block is a track ("1") or not ("0").
     *    'child_sequence_order' ... Indicates whether child sequences are ordered ("1"), unordered ("2") or parallel ("3").
     *    'academic_level_id' ... The id of the academic level of this sequence block.
     *    'order_in_sequence' ... Indicates the order of this sequence block in relation to its siblings within a sequence.
     *          This only applies to blocks nested within ordered sequence blocks. Defaults to '0' in all other scenarios.
     *    'start_date' ... The start date of a sequence block.
     *    'end_date' ... The end date of a sequence block.
     *    'duration' ... The duration of a sequence block in minutes.
     *    'course_id' ... The id of the course that this sequence block is linked to. blank if no course should be linked.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains the following properties:
     *     "sequence_block" ... a map which contains the created sequence block record.
     *     "updated_siblings_order" ... a map which may contain key/value pairs of block-id/modified order-in-sequence
     *          values for blocks within the same ordered sequence as the newly created one.
     *          The array will be empty if the block was created in a non-ordered sequence, or if the block creation had
     *          no side-effects on existing blocks within the same sequence.
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function createSequenceBlock ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');

        //
        // fetch and validate report- and parent-block-data
        //
        $reportId = (int) $this->input->post('report_id');
        $parentBlockId = (int) $this->input->post('parent_sequence_block_id');

        $invReport = $this->invReport->getRowForPrimaryKeyId($reportId);
        if (! $invReport) {
            $this->_printErrorXhrResponse('curriculum_inventory.validate.error.report_does_not_exist');
            return;
        }

        // reject requests for modifying finalized reports
        if ($this->invExport->exists($reportId)) {
            $this->_printErrorXhrResponse('curriculum_inventory.error.cannot_modify_finalized_report');
            return;
        }

        $parentBlock = null;

        if ($parentBlockId) {
            $parentBlock = $this->invSequenceBlock->getRowForPrimaryKeyId($parentBlockId);
            if (! $parentBlock) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.update.error.does_not_exist');
                return;
            }
            // paranoia mode - check if the parent block belongs to the given report
            if ($parentBlock->report_id !== $invReport->report_id) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.create.error.report_mismatch');
                return;
            }
        }

        //
        // fetch and validate the rest of the form post
        //
        $title = trim($this->input->post('title'));
        $description = trim($this->input->post('description'));
        $minimum  = (int) $this->input->post('minimum');
        $maximum = (int) $this->input->post('maximum');
        $required = (int) $this->input->post('required');
        $childSequenceOrder = (int) $this->input->post('child_sequence_order');
        $orderInSequence = (int) $this->input->post('order_in_sequence');
        $academicLevelId = (int) $this->input->post('academic_level');
        $courseId = (int) $this->input->post('course_id');
        $track = (boolean) $this->input->post('track');
        $startDate = trim($this->input->post('start_date'));
        $endDate = trim($this->input->post('end_date'));
        $startDateTs = strtotime($startDate);
        $endDateTs = strtotime($endDate);
        $duration = (int) $this->input->post('duration');
        $hasStartDate = ('' !== $startDate);
        $isInOrderedSequence = ($parentBlock
            && $parentBlock->child_sequence_order == Curriculum_Inventory_Sequence_Block::ORDERED);

        $durationRequired = false;
        $dateRangeRequired = false;
        $course = null;
        if ($courseId) {
            $course = $this->course->getRowForPrimaryKeyId($courseId);
            if (! $course) {
                $this->_printErrorXhrResponse('general.error.course_not_found');
                return;
            }
            if ($course->clerkship_type_id) {
                $durationRequired = true;
                $dateRangeRequired = true;
            }
        }


        if ('' === $title) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.title_missing');
            return;
        }
        if ('' === $description) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.description_missing');
            return;
        }
        if (0 > $minimum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_minimum');
            return;
        }
        if (0 > $maximum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_maximum');
            return;
        }

        if ($minimum > $maximum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.minimum_gt_maximum');
            return;
        }
        if (! in_array($required, array(Curriculum_Inventory_Sequence_Block::REQUIRED,
            Curriculum_Inventory_Sequence_Block::OPTIONAL, Curriculum_Inventory_Sequence_Block::REQUIRED_IN_TRACK))) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.minimum_gt_maximum');
            return;
        }
        if (! in_array($childSequenceOrder, array(Curriculum_Inventory_Sequence_Block::ORDERED,
            Curriculum_Inventory_Sequence_Block::UNORDERED, Curriculum_Inventory_Sequence_Block::PARALLEL))) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_child_sequence_order');
            return;
        }
        if (! $academicLevelId) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.academic_level_missing');
            return;
        }
        if ($course
            && ! $this->inventory->isLinkableCourse($invReport->year, $schoolId, $invReport->report_id, $course->course_id)) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.course_not_linkable');
            return;
        }
        if ($isInOrderedSequence) {
            // perform boundaries check of given order in sequence
            if ($orderInSequence < 1) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_order_in_sequence');
                return;
            }
            $numberOfSiblings = $this->invSequenceBlock->getNumberOfChildren($parentBlock->sequence_block_id);
            if ($orderInSequence > ($numberOfSiblings + 1)) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_order_in_sequence');
                return;
            }
        }


        if ($dateRangeRequired && ! $hasStartDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.missing_start_date');
            return;
        }

        if ($hasStartDate) {
            if ('' === $endDate) { // must provide end date if start date is given
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.missing_end_date');
                return;
            }
            // start and end date must be valid
            if (false === $startDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_start_date');
                return;
            };
            if (false === $startDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_end_date');
                return;
            };

            // start date must not come after end date
            if ($startDateTs > $endDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.start_date_gt_end_date');
                return;
            }
        } else { // if no date range is given then duration becomes required
            $durationRequired = true;
        }

        if ($durationRequired && ! $duration) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_duration');
            return;
        }

        if (0 > $duration) { // if a duration is given then it must be valid
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_duration');
            return;
        }

        // final data massaging to provide proper default values for optional/conditional properties
        $orderInSequence = $isInOrderedSequence ? $orderInSequence : 0;
        $parentBlockId = $parentBlockId ? $parentBlockId : null;
        $courseId = $courseId ? $courseId : null;
        if ($hasStartDate) {
            $startDate = date('Y-m-d', $startDateTs);
            $endDate = date('Y-m-d', $endDateTs);
        } else {
            $startDate = null;
            $endDate = null;
        }

        //
        // create a new sequence block in the db
        //
        $updatedBlockOrder = array();
        $this->db->trans_start();
        if ($isInOrderedSequence) {
            $this->invSequenceBlock->incrementOrderInSequence($orderInSequence, $parentBlock->sequence_block_id);
            $updatedBlockOrder = $this->invSequenceBlock->getBlockOrderInSequence($parentBlock->sequence_block_id, $orderInSequence);
        }
        $blockId = $this->invSequenceBlock->create($reportId, $parentBlockId, $title, $description, $startDate,
            $endDate, $duration, $academicLevelId, $required, $maximum, $minimum, $track, $courseId, $childSequenceOrder,
            $orderInSequence);
        $block = $this->invSequenceBlock->get($blockId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.create.error.general');
            return;
        }

        $rhett['sequence_block'] = $block;
        $rhett['updated_siblings_order'] = $updatedBlockOrder;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action updates a sequence block.
     *
     * It expects the following POST parameters:
     *    'sequence_block_id' ... The sequence block id.
     *    'title' ... The title of the block.
     *    'description' ... A description of the block.
     *    'required' ... Indicates whether the block is required ("1"), optional ("2") or required in track ("3").
     *    'minimum' ... Indicates the number of child sequence blocks that a learner must take.
     *    'maximum' ... Indicates the number of child sequence blocks that a learner can take.
     *    'track' ... Indicates whether this sequence block is a track ("1") or not ("0").
     *    'child_sequence_order' ... Indicates whether child sequences are ordered ("1"), unordered ("2") or parallel ("3").
     *    'academic_level_id' ... The id of the academic level of this sequence block.
     *    'order_in_sequence' ... Indicates the order of this sequence block in relation to its siblings within a sequence.
     *          This only applies to blocks nested within ordered sequence blocks. Defaults to '0' in all other scenarios.
     *    'start_date' ... The start date of a sequence block.
     *    'end_date' ... The end date of a sequence block.
     *    'duration' ... The duration of a sequence block in minutes.
     *    'course_id' ... The id of the course that this sequence block is linked to. blank if no course should be linked.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains the following properties:
     *     "sequence_block" ... a map which contains the updated sequence block record.
     *     "updated_siblings_order" ... a map which may contain key/value pairs of block-id/modified order-in-sequence
     *          values for blocks within the same ordered sequence as the updated one.
     *          The array will be empty if the updated block is part of a non-ordered sequence, or if the update had
     *          no side-effects on other blocks within the same sequence.
     *     "updated_children_order" ... a map which may contain key/value pairs of block-id/modified order-in-sequence
     *          values for children of the updated block. This may be the case if the child-sequence-value of the updated
     *          block changed from or to "ordered", which triggers resorting of the child sequence.
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function updateSequenceBlock ()
    {
        $rhett = array();

        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }

        $schoolId = $this->session->userdata('school_id');

        //
        // fetch and validate report- and parent-block-data
        //
        $blockId = (int) $this->input->post('sequence_block_id');
        $block = $this->invSequenceBlock->getRowForPrimaryKeyId($blockId);
        if (! $block) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.update.error.does_not_exist');
            return;
        }

        // reject requests for modifying finalized reports
        if ($this->invExport->exists($block->report_id)) {
            $this->_printErrorXhrResponse('curriculum_inventory.error.cannot_modify_finalized_report');
            return;
        }

        $invReport = $this->invReport->getRowForPrimaryKeyId($block->report_id);
        $parentBlock = false;
        if ($block->parent_sequence_block_id) {
            $parentBlock = $this->invSequenceBlock->getRowForPrimaryKeyId($block->parent_sequence_block_id);
        }

        //
        // fetch and validate the rest of the form post
        //
        $title = trim($this->input->post('title'));
        $description = trim($this->input->post('description'));
        $minimum  = (int) $this->input->post('minimum');
        $maximum = (int) $this->input->post('maximum');
        $required = (int) $this->input->post('required');
        $childSequenceOrder = (int) $this->input->post('child_sequence_order');
        $orderInSequence = (int) $this->input->post('order_in_sequence');
        $academicLevelId = (int) $this->input->post('academic_level');
        $courseId = (int) $this->input->post('course_id');
        $track = (boolean) $this->input->post('track');
        $startDate = trim($this->input->post('start_date'));
        $endDate = trim($this->input->post('end_date'));
        $startDateTs = strtotime($startDate);
        $endDateTs = strtotime($endDate);
        $duration = (int) $this->input->post('duration');
        $hasStartDate = ('' !== $startDate);
        $isInOrderedSequence = ($parentBlock
            && $parentBlock->child_sequence_order == Curriculum_Inventory_Sequence_Block::ORDERED);

        $durationRequired = false;
        $dateRangeRequired = false;
        $course = null;
        if ($courseId) {
            $course = $this->course->getRowForPrimaryKeyId($courseId);
            if (! $course) {
                $this->_printErrorXhrResponse('general.error.course_not_found');
                return;
            }
            if ($course->clerkship_type_id) {
                $durationRequired = true;
                $dateRangeRequired = true;
            }
        }

        if ('' === $title) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.title_missing');
            return;
        }
        if ('' === $description) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.description_missing');
            return;
        }
        if (0 > $minimum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_minimum');
            return;
        }
        if (0 > $maximum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_maximum');
            return;
        }

        if ($minimum > $maximum) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.minimum_gt_maximum');
            return;
        }
        if (! in_array($required, array(Curriculum_Inventory_Sequence_Block::REQUIRED,
            Curriculum_Inventory_Sequence_Block::OPTIONAL, Curriculum_Inventory_Sequence_Block::REQUIRED_IN_TRACK))) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.minimum_gt_maximum');
            return;
        }
        if (! in_array($childSequenceOrder, array(Curriculum_Inventory_Sequence_Block::ORDERED,
            Curriculum_Inventory_Sequence_Block::UNORDERED, Curriculum_Inventory_Sequence_Block::PARALLEL))) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_child_sequence_order');
            return;
        }
        if (! $academicLevelId) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.academic_level_missing');
            return;
        }
        if ($course
            && ($course->course_id != $block->course_id) // check first if course has changed
            && ! $this->inventory->isLinkableCourse($invReport->year, $schoolId, $invReport->report_id, $course->course_id)) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.course_not_linkable');
            return;
        }
        if ($isInOrderedSequence) {
            // perform boundaries check of given order in sequence
            if ($orderInSequence < 1) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_order_in_sequence');
                return;
            }
            $numberOfSiblings = $this->invSequenceBlock->getNumberOfChildren($parentBlock->sequence_block_id);
            if ($orderInSequence > ($numberOfSiblings + 1)) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_order_in_sequence');
                return;
            }
        }

        if ($dateRangeRequired && ! $hasStartDate) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.missing_start_date');
            return;
        }

        if ($hasStartDate) {
            if ('' === $endDate) { // must provide end date if start date is given
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.missing_end_date');
                return;
            }
            // start and end date must be valid
            if (false === $startDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_start_date');
                return;
            };
            if (false === $startDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_end_date');
                return;
            };

            // start date must not come after end date
            if ($startDateTs > $endDateTs) {
                $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.start_date_gt_end_date');
                return;
            }
        } else { // if no date range is given then duration becomes required
            $durationRequired = true;
        }

        if ($durationRequired && ! $duration) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_duration');
            return;
        }
        if (0 > $duration) { // if a duration is given then it must be valid
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.validate.error.invalid_duration');
            return;
        }

        // final data massaging to provide proper default values for optional/conditional properties
        $orderInSequence = $isInOrderedSequence ? $orderInSequence : 0;
        $courseId = $courseId ? $courseId : null;
        if ($hasStartDate) {
            $startDate = date('Y-m-d', $startDateTs);
            $endDate = date('Y-m-d', $endDateTs);
        } else {
            $startDate = null;
            $endDate = null;
        }

        $childSequenceOrderHasChanged = ($block->child_sequence_order != $childSequenceOrder);
        $orderInSequenceHasChanged = ($block->order_in_sequence != $orderInSequence);

        $data = array();
        $data['title'] = $title;
        $data['description'] = $description;
        $data['required'] = $required;
        $data['maximum'] = $maximum;
        $data['minimum'] = $minimum;
        $data['track'] = $track;
        $data['course_id'] = $courseId;
        $data['academic_level_id'] = $academicLevelId;
        $data['child_sequence_order'] = $childSequenceOrder;
        $data['order_in_sequence'] = $orderInSequence;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['duration'] = $duration;


        $updatedChildrenOrder = array();
        $updatedSiblingsOrder = array();
        //
        // create a new sequence block in the db
        //
        $this->db->trans_start();
        // if the given block is in an ordered sequence, and its place within that sequence has changed
        // then we must resort the whole sequence.
        if ($isInOrderedSequence && $orderInSequenceHasChanged) {
            $this->invSequenceBlock->decrementOrderInSequence($block->order_in_sequence, $parentBlock->sequence_block_id);
            $this->invSequenceBlock->incrementOrderInSequence($orderInSequence, $parentBlock->sequence_block_id);
            $boundaries = array((int) $block->order_in_sequence, $orderInSequence);
            $updatedSiblingsOrder = $this->invSequenceBlock->getBlockOrderInSequence($parentBlock->sequence_block_id,
                min($boundaries), max($boundaries));
        }
        if ($childSequenceOrderHasChanged) {
            if ($block->child_sequence_order == Curriculum_Inventory_Sequence_Block::ORDERED) {
                if ($this->invSequenceBlock->getNumberOfChildren($block->sequence_block_id)) {
                    // if the block's child sequence is changing FROM the ordered TO an unordered state
                    // then the sequence order for each child must be zeroed out
                    $this->invSequenceBlock->setOrderToZeroInSequence($block->sequence_block_id);
                    $updatedChildrenOrder = $this->invSequenceBlock->getBlockOrderInSequence($block->sequence_block_id);
                }
            } elseif ($childSequenceOrder == Curriculum_Inventory_Sequence_Block::ORDERED) {
                // if the block's child sequence is changing FROM an unordered TO the ordered state
                // the we must sort all of its children and update each child's order-in-sequence value
                // accordingly.
                $children = $this->invSequenceBlock->getChildren($block->sequence_block_id);
                $n = count($children);
                if ($n) {
                    usort($children, 'Curriculum_Inventory_Sequence_Block::defaultSortSequenceBlocks');
                    for ($i = 0; $i < $n; $i++) {
                        $this->invSequenceBlock->update($children[$i]['sequence_block_id'],
                            array('order_in_sequence' => ($i + 1)));
                    }
                    $updatedChildrenOrder = $this->invSequenceBlock->getBlockOrderInSequence($block->sequence_block_id);
                }
            }
        }
        $this->invSequenceBlock->update($block->sequence_block_id, $data);
        $block = $this->invSequenceBlock->get($blockId);
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.update.error.general');
            return;
        }

        $rhett['sequence_block'] = $block;
        $rhett['updated_siblings_order'] = $updatedSiblingsOrder;
        $rhett['updated_children_order'] = $updatedChildrenOrder;

        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }

    /**
     * This action deletes a given sequence block and all its descendants.
     *
     * It expects the following POST parameters:
     *     'sequence_block_id' ... The id of the sequence block to delete.
     *
     * This method prints out a result object as JSON-formatted text.
     *
     * On success, the object contains the following properties:
     *     "sequence_block_id" ... the id of the deleted sequence block.
     *     "updated_siblings_order" ... a map which may contain key/value pairs of block-id/modified order-in-sequence
     *          values for blocks within the same ordered sequence as the deleted one.
     *          The array will be empty if the block was deleted from a non-ordered sequence, or if the block deletion had
     *          no side-effects on other blocks within the same sequence.
     * On failure, the object contains a property "error", which contains an error message.
     */
    public function deleteSequenceBlock ()
    {
        // authorization check
        if (! $this->session->userdata('has_admin_access')) {
            $this->_printAuthorizationFailedXhrResponse();
            return;
        }


        // input validation
        $sequenceBlockId = (int) $this->input->post('sequence_block_id');
        $block = $this->invSequenceBlock->getRowForPrimaryKeyId($sequenceBlockId);
        if (! $block) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.error.does_not_exist');
            return;
        }

        // reject requests for modifying finalized reports
        if ($this->invExport->exists($block->report_id)) {
            $this->_printErrorXhrResponse('curriculum_inventory.error.cannot_modify_finalized_report');
            return;
        }

        $isInOrderedSequence = false;
        $updatedBlockOrder = array();
        if ($block->parent_sequence_block_id) {
            $parent = $this->invSequenceBlock->getRowForPrimaryKeyId($block->parent_sequence_block_id);
            if (Curriculum_Inventory_Sequence_Block::ORDERED == $parent->child_sequence_order) {
                $isInOrderedSequence = true;
            }
        }


        // delete the report and associated records
        $this->db->trans_start();
        $this->invSequenceBlock->delete($sequenceBlockId);
        if ($isInOrderedSequence) {
            $this->invSequenceBlock->decrementOrderInSequence($block->order_in_sequence, $block->parent_sequence_block_id);
            $updatedBlockOrder = $this->invSequenceBlock->getBlockOrderInSequence($block->parent_sequence_block_id, $block->order_in_sequence);

        }
        $this->db->trans_complete();
        if (false === $this->db->trans_status()) {
            $this->_printErrorXhrResponse('curriculum_inventory.sequence_block.delete.error.general');
            return;
        }
        $rhett = array(
            'sequence_block_id' => $block->sequence_block_id,
            'updated_siblings_order' => $updatedBlockOrder
        );
        header("Content-Type: text/plain");
        echo json_encode($rhett);
    }
}
