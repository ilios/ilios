<?php
/**
 * Curriculum Inventory Exporter.
 *
 * Provides functionality for generating curriculum inventory reports and for exporting reports to XML according
 * to the MedBiquitous specification.
 *
 * @see Curriculum_Inventory_Manager
 *
 * @link http://www.medbiq.org/sites/default/files/files/CurriculumInventorySpecification.pdf
 * @link http://ns.medbiq.org/curriculuminventory/v1/curriculuminventory.xsd
 *
 * @todo Break this class up in two - one for report generation and one for exporting to XML. [ST 2013/07/22]
 */
class Ilios_CurriculumInventory_Exporter
{
    /**
     * The CodeIgniter super object.
     * @var CI_Controller
     */
    protected $_ci;

    /**
     * Constructor.
     * @param CI_Controller $ci The CodeIgniter super object.
     */
    public function __construct (CI_Controller &$ci)
    {
        $this->_ci =& $ci;

        // conditionally load all necessary DAOs
        if (! property_exists($this->_ci, 'clerkshipType')) {
            $this->_ci->load->model('Course_Clerkship_Type', 'clerkshipType', true);
        }
        if (! property_exists($this->_ci, 'inventory')) {
            $this->_ci->load->model('Curriculum_Inventory', 'inventory', true);
        }
        if (! property_exists($this->_ci, 'invReport')) {
            $this->_ci->load->model('Curriculum_Inventory_Report', 'invReport', true);
        }
        if (! property_exists($this->_ci, 'invAcademicLevel')) {
            $this->_ci->load->model('Curriculum_Inventory_Academic_Level', 'invAcademicLevel', true);
        }
        if (! property_exists($this->_ci, 'invInstitution')) {
            $this->_ci->load->model('Curriculum_Inventory_Institution', 'invInstitution', true);
        }
        if (! property_exists($this->_ci, 'invSequence')) {
            $this->_ci->load->model('Curriculum_Inventory_Sequence', 'invSequence', true);
        }
        if (! property_exists($this->_ci, 'invSequenceBlock')) {
            $this->_ci->load->model('Curriculum_Inventory_Sequence_Block', 'invSequenceBlock', true);
        }
    }


    /**
     * Retrieves a curriculum inventory in a data structure that lends itself for an easy transformation into
     * XML-formatted report.
     *
     * @param int $reportId The report id.
     * @return array An associated array, containing the inventory.
     *     Data is keyed off by:
     *         'report' ... An associative array holding various report-related properties, such as id, domain etc
     *         'program' ... An object representing the program associated with the report
     *         'institution' ... An object representing the curriculum inventory's owning institution
     *         'events' ... An array of events, keyed off by event id. Each event is represented as assoc. array.
     *         'expectations' ... An associative array of arrays, each sub-array containing a
     *                            list of a different type of "competency object" within the curriculum.
     *                            These types are program objectives, course objectives and session objectives.
     *                            The keys for these type-specific sub-arrays are:
     *             'program_objectives'
     *             'course_objectives'
     *             'session_objectives'
     *         'framework' ... The competency framework data set.
     *             'includes' ... Identifiers of the various competency objects that get referenced in the framework.
     *                 'pcrs_ids'
     *                 'program_objective_ids'
     *                 'course_objective_ids'
     *                 'session_objective_ids'
     *             'relations' ... Relations between the various competencies within the framework
     *                 'program_objectives_to_pcrs'
     *                 'course_objectives_to_program_objectives'
     *                 'session_objectives_to_course_objectives'
     *         'academic_levels' ... An array of academic levels used in the curriculum.
     *                               Each academic level is represented by an associative array.
     *         'sequence'  ... the inventory sequence object
     *         'sequence_blocks' An array of sequence block. Each sequence block is represented as associative array.
     * @throws Ilios_Exception
     * @see CurriculumInventoryReporter::createReportXml();
     */
    public function getCurriculumInventory ($reportId)
    {
        //
        // load inventory from various sources
        //
        $invReport = $this->_ci->invReport->getRowForPrimaryKeyId($reportId);
        if (! isset($invReport)) {
            throw new Ilios_Exception('Could not load the report for the given id ( ' . $reportId . ')');
        }

        $program = $this->_ci->program->getRowForPrimaryKeyId($invReport->program_id);
        if (! isset($program)) {
            throw new Ilios_Exception('Could not load program for program id ( ' . $program->program_id . ')');
        }

        $invInstitution  = $this->_ci->invInstitution->getRowForPrimaryKeyId($program->owning_school_id);
        if (! isset($invInstitution)) {
            throw new Ilios_Exception('Could not load curriculum institution for school id ( ' . $program->owning_school_id . ')');
        }
        $invSequence = $this->_ci->invSequence->getRowForPrimaryKeyId($reportId);
        if (! isset($invSequence)) {
            throw new Ilios_Exception('Could not load curriculum sequence for report id ( ' . $reportId . ')');
        }

        $events = $this->_ci->inventory->getEvents($reportId);
        $keywords = $this->_ci->inventory->getEventKeywords($reportId);
        $levels = $this->_ci->invAcademicLevel->getAppliedLevels($reportId);
        $sequenceBlocks = $this->_ci->invSequenceBlock->getBlocks($reportId);
        $eventReferences = $this->_ci->inventory->getEventReferencesForSequenceBlocks($reportId);

        $programObjectives = $this->_ci->inventory->getProgramObjectives($reportId);
        $sessionObjectives = $this->_ci->inventory->getSessionObjectives($reportId);
        $courseObjectives = $this->_ci->inventory->getCourseObjectives($reportId);

        $compRefsForSeqBlocks = $this->_ci->inventory->getCompetencyObjectReferencesForSequenceBlocks($reportId);
        $compRefsForEvents = $this->_ci->inventory->getCompetencyObjectReferencesForEvents($reportId);

        // The various objective type are all "Competency Objects" in the context of reporting the curriculum inventory.
        // The are grouped in the "Expectations" section of the report, lump 'em together here.
        $expectations = array();
        $expectations['program_objectives'] = $programObjectives;
        $expectations['session_objectives'] = $sessionObjectives;
        $expectations['course_objectives'] = $courseObjectives;


        // Build out the competency framework information and added to $expectations.
        $pcrs = $this->_ci->inventory->getPcrs($reportId);

        $pcrsIds = array_keys($pcrs);
        $programObjectiveIds = array_keys($programObjectives);
        $courseObjectiveIds = array_keys($courseObjectives);
        $sessionObjectiveIds = array_keys($sessionObjectives);
        $includes = array(
            'pcrs_ids' => array(),
            'program_objective_ids' => array(),
            'course_objective_ids' => array(),
            'session_objective_ids' => array(),
        );
        $relations = array(
            'program_objectives_to_pcrs' => array(),
            'course_objectives_to_program_objectives' => array(),
            'session_objectives_to_course_objectives' => array(),
        );

        $rel = $this->_ci->inventory->getProgramObjectivesToPcrsRelations($programObjectiveIds, $pcrsIds);
        $relations['program_objectives_to_pcrs'] = $rel['relations'];
        $includes['pcrs_ids'] = $rel['pcrs_ids'];
        $includes['program_objective_ids'] = $rel['program_objective_ids'];
        $rel = $this->_ci->inventory->getCourseObjectivesToProgramObjectivesRelations($courseObjectiveIds,
            $programObjectiveIds);
        $relations['course_objectives_to_program_objectives'] = $rel['relations'];
        $includes['program_objective_ids'] = $includes['program_objective_ids'] + $rel['program_objective_ids'];
        $includes['course_objective_ids'] = $rel['course_objective_ids'];
        $rel = $this->_ci->inventory->getSessionObjectivesToCourseObjectivesRelations($sessionObjectiveIds,
            $courseObjectiveIds);
        $relations['session_objectives_to_course_objectives'] = $rel['relations'];
        $includes['course_objective_ids'] = $includes['course_objective_ids'] + $rel['course_objective_ids'];
        $includes['session_objective_ids'] = $rel['session_objective_ids'];

        $expectations['framework'] = array(
            'includes' => $includes,
            'relations' => $relations,
        );

        // report (and some program) properties
        $report = array();
        $report['id'] = $invReport->year . '-' . $program->program_id . '-' . time(); // report id format: "<academic year>-<program id>-<current timestamp>"
        $report['domain'] = $this->_ci->config->item('curriculum_inventory_institution_domain');
        $report['date'] = date('Y-m-d');
        $report['name'] = $invReport->name;
        $report['description'] = $invReport->description;
        $report['start_date'] = $invReport->start_date;
        $report['end_date'] = $invReport->end_date;

        $supportingLink = $this->_ci->config->item('curriculum_inventory_supporting_link');
        if ($supportingLink) {
            $report['supporting_link'] = $supportingLink;
        }

        //
        // transmogrify inventory data for reporting and fill in the blanks
        //

        // add keywords to event
        $events = $this->_addKeywordsToEvents($events, $keywords);
        $events = $this->_addCompetencyObjectReferencesToEvents($events, $compRefsForEvents);

        $sequenceBlocks = $this->_addEventAndCompetencyObjectReferencesToSequenceBlocks($sequenceBlocks,
            $eventReferences, $compRefsForSeqBlocks);

        // transform sequence blocks from a flat list into a nested structure
        $sequenceBlocks = $this->_ci->invSequenceBlock->buildSequenceBlockHierarchy($sequenceBlocks);

        //
        // aggregate inventory into single return-array
        //
        $rhett = array();
        $rhett['report'] = $report;
        $rhett['program'] = $program;
        $rhett['expectations'] = $expectations;
        $rhett['institution'] = $invInstitution;
        $rhett['sequence'] = $invSequence;
        $rhett['sequence_blocks'] = $sequenceBlocks;
        $rhett['events'] = $events;
        $rhett['academic_levels'] = $levels;
        return $rhett;
    }

    /**
     * Creates an XML representation of the given curriculum inventory.
     * @param array $inventory An associative array representing the entire curriculum inventory.
     *     The inventory is expected to be structured like the output of <code>getCurriculumInventory()<code>.
     * @return DOMDocument The generated XML document.
     * @throws DomException
     * @see Ilios_CurriculumInventory_Exporter::getCurriculumInventory()
     */
    public function createXmlReport (array $inventory)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $rootNode = $dom->createElementNS('http://ns.medbiq.org/curriculuminventory/v1/', 'CurriculumInventory');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $rootNode->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation',
            'http://ns.medbiq.org/curriculuminventory/v1/ curriculuminventory.xsd');
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
        $reportIdNode->setAttribute('domain', "idd:{$inventory['report']['domain']}:cireport");
        $rootNode->appendChild($reportIdNode);

        // Institution
        $institutionNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:Institution');
        $rootNode->appendChild($institutionNode);
        $institutionNameNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:InstitutionName');
        $institutionNameNode->appendChild($dom->createTextNode($inventory['institution']->name));
        $institutionNode->appendChild($institutionNameNode);
        $institutionIdNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:InstitutionID', $inventory['institution']->aamc_code);
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
        $programNameNode->appendChild($dom->createTextNode($inventory['program']->title));
        $programNode->appendChild($programNameNode);
        $programIdNode = $dom->createElement('ProgramID', $inventory['program']->program_id);
        $programIdNode->setAttribute('domain', "idd:{$inventory['report']['domain']}:program");
        $programNode->appendChild($programIdNode);

        //
        // various other report attributes
        //
        $titleNode = $dom->createElement('Title');
        $titleNode->appendChild($dom->createTextNode($inventory['report']['name']));
        $rootNode->appendChild($titleNode);
        $reportDateNode = $dom->createElement('ReportDate', date('Y-m-d'));
        $rootNode->appendChild($reportDateNode);
        $reportingStartDateNode = $dom->createElement('ReportingStartDate', $inventory['report']['start_date']);
        $rootNode->appendChild($reportingStartDateNode);
        $reportingEndDateNode = $dom->createElement('ReportingEndDate', $inventory['report']['end_date']);
        $rootNode->appendChild($reportingEndDateNode);
        $languageNode = $dom->createElement('Language', 'en-US');
        $rootNode->appendChild($languageNode);
        $descriptionNode = $dom->createElement('Description');
        $descriptionNode->appendChild($dom->createTextNode($inventory['report']['description']));
        $rootNode->appendChild($descriptionNode);
        // default supporting link url to the site url of this Ilios instance.
        if (array_key_exists('supporting_link', $inventory['report'])) {
            $supportingLinkNode = $dom->createElement('SupportingLink', $inventory['report']['supporting_link']);
            $rootNode->appendChild($supportingLinkNode);
        }
        //
        // Events
        //
        $domain = $inventory['report']['domain'];
        $eventsNode = $dom->createElement('Events');
        $rootNode->appendChild($eventsNode);
        foreach ($inventory['events'] as $event) {
            $eventNode = $dom->createElement('Event');
            $eventsNode->appendChild($eventNode);
            $eventNode->setAttribute('id', 'E' . $event['event_id']);
            $eventTitleNode = $dom->createElement('Title');
            $eventNode->appendChild($eventTitleNode);
            $eventTitleNode->appendChild($dom->createTextNode($event['title']));
            $eventDurationNode = $dom->createElement('EventDuration', 'PT' . $event['duration'] . 'M');
            $eventNode->appendChild($eventDurationNode);
            if ('' !== trim($event['description'])) {
                $descriptionNode = $dom->createElement('Description');
                $eventNode->appendChild($descriptionNode);
                $descriptionNode->appendChild($dom->createTextNode(trim(strip_tags($event['description']))));
            }
            // keywords
            if (array_key_exists('keywords', $event)) {
                foreach ($event['keywords'] as $keyword) {
                    $keywordNode = $dom->createElement('Keyword');
                    $eventNode->appendChild($keywordNode);
                    $keywordNode->setAttribute('hx:source', 'MeSH');
                    $keywordNode->setAttribute('hx:id', $keyword['mesh_descriptor_uid']);
                    $descriptorNode = $dom->createElementNS('http://ns.medbiq.org/lom/extend/v1/', 'string');
                    $keywordNode->appendChild($descriptorNode);
                    $descriptorNode->appendChild($dom->createTextNode($keyword['name']));
                }
            }

            // competency object references
            if (array_key_exists('competency_object_references', $event)) {
                foreach ($event['competency_object_references']['program_objectives'] as $id) {
                    $uri = $this->_createCompetencyObjectUri($domain, $id, 'program_objective');
                    $this->_createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
                foreach ($event['competency_object_references']['course_objectives'] as $id) {
                    $uri = $this->_createCompetencyObjectUri($domain, $id, 'course_objective');
                    $this->_createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
                foreach ($event['competency_object_references']['session_objectives'] as $id) {
                    $uri = $this->_createCompetencyObjectUri($domain, $id, 'session_objective');
                    $this->_createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
            }

            // resource types are not implemented.

            // instructional- or assessment-method
            //
            // NOTE: unmapped session types to AAMC methods will result in empty values in the
            // <InstructionalMethod> or <AssessmentMethod> elements.
            // Which will result the report being rejected on import.
            // The alternatives would have been to:
            // (a) exclude events with unknown AAMC methods.
            // (b) raise an exception on report generation.
            // Neither of which are IMO preferable to the current approach to "kick the bucket down the road" at this point.
            // Option (b) may be something could be implemented at a later point, once the AAMC's CI tool and business rules
            // are less of a moving target than what they are now.
            // [ST 2013/09/07]
            if ($event['is_assessment_method']) {
                $assessmentMethodNode = $dom->createElement('AssessmentMethod');
                $eventNode->appendChild($assessmentMethodNode);
                //
                // from the spec:
                // AssessmentMethod has the following attribute
                //
                // purpose
                // Indicates whether the assessment is used for formative or
                // summative assessment. Use of the purpose attribute is required.
                // Valid values are Formative and Summative.
                //
                switch ($event['assessment_option_name']) {
                    case 'formative' :
                        $assessmentMethodNode->setAttribute('purpose', 'Formative');
                        break;
                    case 'summative' :
                    default:
                        $assessmentMethodNode->setAttribute('purpose', 'Summative');
                }
                $assessmentMethodNode->appendChild($dom->createTextNode($event['method_id']));
            } else {
                $instructionalMethodNode = $dom->createElement('InstructionalMethod');
                $eventNode->appendChild($instructionalMethodNode);
                $instructionalMethodNode->setAttribute('primary', 'true');
                $instructionalMethodNode->appendChild($dom->createTextNode($event['method_id']));
            }
        }

        //
        // Expectations
        //
        $expectationsNode = $dom->createElement('Expectations');
        $rootNode->appendChild($expectationsNode);
        // program objectives
        foreach ($inventory['expectations']['program_objectives'] as $programObjective) {
            $uri = $this->_createCompetencyObjectUri($domain, $programObjective['objective_id'], 'program_objective');
            $this->_createCompetencyObjectNode($dom, $expectationsNode, $programObjective['title'], $uri,
                'program-level-competency');
        }
        // course objectives
        foreach ($inventory['expectations']['course_objectives'] as $courseObjective) {
            $uri = $this->_createCompetencyObjectUri($domain, $courseObjective['objective_id'], 'course_objective');
            $this->_createCompetencyObjectNode($dom, $expectationsNode, $courseObjective['title'], $uri,
                'sequence-block-level-competency');
        }
        // session objectives
        foreach ($inventory['expectations']['session_objectives'] as $sessionObjective) {
            $uri = $this->_createCompetencyObjectUri($domain, $sessionObjective['objective_id'], 'session_objective');
            $this->_createCompetencyObjectNode($dom, $expectationsNode, $sessionObjective['title'], $uri,
                'event-level-competency');
        }
        // add competency framework
        $this->_createCompetencyFrameworkNode($dom, $expectationsNode, $inventory);
        //
        // Academic Levels
        //
        $academicLevelsNode = $dom->createElement('AcademicLevels');
        $rootNode->appendChild($academicLevelsNode);
        $levelsInProgramNode = $dom->createElement('LevelsInProgram', count($inventory['academic_levels']));
        $academicLevelsNode->appendChild($levelsInProgramNode);
        foreach ($inventory['academic_levels'] as $level) {
            $levelNode = $dom->createElement('Level');
            $academicLevelsNode->appendChild($levelNode);
            $levelNode->setAttribute('number', $level['level']);
            $labelNode = $dom->createElement('Label');
            $levelNode->appendChild($labelNode);
            $labelNode->appendChild($dom->createTextNode($level['name']));
            if ('' !== trim($level['description'])) {
                $descriptionNode = $dom->createElement('Description');
                $levelNode->appendChild($descriptionNode);
                $descriptionNode->appendChild($dom->createTextNode($level['description']));
            }
        }
        //
        // Sequence
        //
        $sequenceNode = $dom->createElement('Sequence');
        $rootNode->appendChild($sequenceNode);
        if ('' !== trim($inventory['sequence']->description)) {
            $sequenceDescriptionNode = $dom->createElement('Description');
            $sequenceNode->appendChild($sequenceDescriptionNode);
            $sequenceDescriptionNode->appendChild($dom->createTextNode($inventory['sequence']->description));
        }
        foreach ($inventory['sequence_blocks'] as $block) {
            $this->_createSequenceBlockNode($dom, $sequenceNode, $block, $inventory);
        }

        //
        // Integration - currently not supported
        //
        return $dom;
    }

    /**
     * Loads the curriculum inventory for a given report and exports it as XML document.
     * @param int $reportId The report id.
     * @return DomDocument The fully populated report.
     * @throws DomException
     * @throws Ilios_Exception
     * @see Ilios_CurriculumInventory_Exporter::getCurriculumInventory()
     * @see Ilios_CurriculumInventory_Exporter::createXmlReport()
     */
    public function getXmlReport ($reportId)
    {
        $inventory = $this->getCurriculumInventory($reportId);
        return $this->createXmlReport($inventory);
    }

    /**
     * Adds keywords to events.
     * @param array $events A list of events.
     * @param array $keywords A list of keywords.
     * @return array The events with the keywords added.
     */
    protected function _addKeywordsToEvents (array $events, array $keywords)
    {
        foreach ($keywords as $keyword) {
            $eventId = $keyword['event_id'];
            if (! array_key_exists($eventId, $events)) {
                continue;
            }
            if (! array_key_exists('keywords', $events[$eventId])) {
                $events[$eventId]['keywords'] = array();
            }
            $events[$eventId]['keywords'][] = $keyword;
        }
        return $events;
    }

    /**
     * Adds competency objects references to events.
     * @param array $events A list of events.
     * @param array $references A list of competency object references.
     * @return array The events with references added.
     */
    protected function _addCompetencyObjectReferencesToEvents (array $events, array $references)
    {
        $sessionIds = array_keys($events);
        for ($i = 0, $n = count($sessionIds); $i < $n; $i++) {
            $sessionId = $sessionIds[$i];
            if (array_key_exists($sessionId, $references)) {
                $events[$sessionId]['competency_object_references'] = $references[$sessionId];
            }
        }
        return $events;
    }

    /**
     * Adds event references and competency object references to sequence blocks
     * @param array $sequenceBlocks A list of sequence blocks.
     * @param array $eventReferences A list of event references.
     * @param array $competencyObjectReferences A list of competency object references.
     * @return array The sequence blocks with references added.
     */
    protected function _addEventAndCompetencyObjectReferencesToSequenceBlocks (array $sequenceBlocks,
                                                                               array $eventReferences,
                                                                               array $competencyObjectReferences)
    {
        for ($i = 0, $n = count($sequenceBlocks); $i < $n; $i++) {
            // link to events
            $sequenceBlockId = $sequenceBlocks[$i]['sequence_block_id'];
            if (array_key_exists($sequenceBlockId, $eventReferences)) {
                $sequenceBlocks[$i]['event_references'] = $eventReferences[$sequenceBlockId];
            } else {
                $sequenceBlocks[$i]['event_references'] = array();
            }
            // link to competency objects
            if (array_key_exists($sequenceBlockId, $competencyObjectReferences)) {
                $sequenceBlocks[$i]['competency_object_references'] = $competencyObjectReferences[$sequenceBlockId];
            }
        }
        return $sequenceBlocks;
    }

    /**
     * Creates the competency framework node and child-nodes, and adds them to a given parent node (<Expectations>).
     * @param DomDocument $dom
     * @param DomElement $parentNode
     * @param array $inventory
     */
    protected function _createCompetencyFrameworkNode (DomDocument $dom, DomElement $parentNode, array $inventory)
    {
        // competency framework
        $competencyFrameworkNode = $dom->createElement('CompetencyFramework');
        $parentNode->appendChild($competencyFrameworkNode);

        // lom
        $lomNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'lom');
        $competencyFrameworkNode->appendChild($lomNode);
        $lomGeneralNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'general');
        $lomNode->appendChild($lomGeneralNode);
        $lomIdentifierNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM','identifier');
        $lomGeneralNode->appendChild($lomIdentifierNode);
        $lomCatalogNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'catalog', 'URI');
        $lomIdentifierNode->appendChild($lomCatalogNode);
        $frameworkUri = "http://{$inventory['report']['domain']}/competency_framework/{$inventory['report']['id']}";
        $lomEntryNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM','entry', $frameworkUri);
        $lomIdentifierNode->appendChild($lomEntryNode);
        $lomTitleNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM','title');
        $lomGeneralNode->appendChild($lomTitleNode);
        $lomStringNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'string');
        $lomTitleNode->appendChild($lomStringNode);
        $title = 'Competency Framework for ' . $inventory['report']['name'];
        $lomStringNode->appendChild($dom->createTextNode($title));

        // includes
        $domain = $inventory['report']['domain'];
        $competencyIds = $inventory['expectations']['framework']['includes']['pcrs_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->_createPcrsUri($id);
            $this->_createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $inventory['expectations']['framework']['includes']['program_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->_createCompetencyObjectUri($domain, $id, 'program_objective');
            $this->_createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $inventory['expectations']['framework']['includes']['course_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->_createCompetencyObjectUri($domain, $id, 'course_objective');
            $this->_createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $inventory['expectations']['framework']['includes']['session_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->_createCompetencyObjectUri($domain, $id, 'session_objective');
            $this->_createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        // relations
        $relations = $inventory['expectations']['framework']['relations']['program_objectives_to_pcrs'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->_createCompetencyObjectUri($domain, $relation['rel1'], 'program_objective');
            $relUri2 = $this->_createPcrsUri($relation['rel2']);
            $relationshipUri = $this->_createRelationshipUri('related');
            $this->_createCompetencyFrameworkRelationNode($dom, $competencyFrameworkNode, $relUri1, $relUri2,
                $relationshipUri);
        }
        $relations = $inventory['expectations']['framework']['relations']['course_objectives_to_program_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->_createCompetencyObjectUri($domain, $relation['rel1'], 'course_objective');
            $relUri2 = $this->_createCompetencyObjectUri($domain, $relation['rel2'], 'program_objective');
            $relationshipUri = $this->_createRelationshipUri('broader');
            $this->_createCompetencyFrameworkRelationNode($dom, $competencyFrameworkNode, $relUri1, $relUri2,
                $relationshipUri);
        }
        $relations = $inventory['expectations']['framework']['relations']['session_objectives_to_course_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->_createCompetencyObjectUri($domain, $relation['rel1'], 'session_objective');
            $relUri2 = $this->_createCompetencyObjectUri($domain, $relation['rel2'], 'course_objective');
            $relationshipUri = $this->_createRelationshipUri('broader');
            $this->_createCompetencyFrameworkRelationNode($dom, $competencyFrameworkNode, $relUri1, $relUri2,
                $relationshipUri);
        }
    }

    /**
     * Recursively creates and appends sequence block nodes to the XML document
     * @param DomDocument $dom the document object
     * @param DomElement $sequenceNode the sequence DOM node to append to
     * @param array $block the current sequence block
     * @param array $inventory the inventory array
     * @param DomElement|null $parentSequenceBlockNode the DOM node representing the parent sequence block (NULL if n/a)
     * @param int $order of this sequence block in relation to other nested sequence blocks. '0' if n/a.
     */
    protected function _createSequenceBlockNode (DomDocument $dom, DomElement $sequenceNode,
                                                array $block, array $inventory,
                                                DomElement $parentSequenceBlockNode = null, $order = 0)
    {
        $sequenceBlockNode = $dom->createElement('SequenceBlock');
        $sequenceNode->appendChild($sequenceBlockNode);
        // append a reference to _this_ sequence block to the parent sequence block
        if (isset($parentSequenceBlockNode)) {
            $ref = "/CurriculumInventory/Sequence/SequenceBlock[@id='{$block['sequence_block_id']}']";
            $sequenceBlockReferenceNode = $dom->createElement('SequenceBlockReference', $ref);
            $parentSequenceBlockNode->appendChild($sequenceBlockReferenceNode);
            if ($order) {
                $sequenceBlockReferenceNode->setAttribute('order', $order);
            }
        }
        $sequenceBlockNode->setAttribute('id', $block['sequence_block_id']);
        switch ($block['required']) {
            case Curriculum_Inventory_Sequence_Block::OPTIONAL :
                $sequenceBlockNode->setAttribute('required', 'Optional');
                break;
            case Curriculum_Inventory_Sequence_Block::REQUIRED :
                $sequenceBlockNode->setAttribute('required', 'Required');
                break;
            case Curriculum_Inventory_Sequence_Block::REQUIRED_IN_TRACK :
                $sequenceBlockNode->setAttribute('required', 'Required In Track');
                break;
        }
        switch ($block['child_sequence_order']) {
            case Curriculum_Inventory_Sequence_Block::ORDERED :
                $sequenceBlockNode->setAttribute('order', 'Ordered');
                break;
            case Curriculum_Inventory_Sequence_Block::UNORDERED :
                $sequenceBlockNode->setAttribute('order', 'Unordered');
                break;
            case Curriculum_Inventory_Sequence_Block::PARALLEL :
                $sequenceBlockNode->setAttribute('order', 'Parallel');
                break;
        }

        $sequenceBlockNode->setAttribute('minimum', $block['minimum']);
        $sequenceBlockNode->setAttribute('maximum', $block['maximum']);

        if ($block['track']) {
            $sequenceBlockNode->setAttribute('track', 'true');
        } else {
            $sequenceBlockNode->setAttribute('track', 'false');
        }

        $titleNode = $dom->createElement('Title');
        $sequenceBlockNode->appendChild($titleNode);
        $titleNode->appendChild($dom->createTextNode($block['title']));

        if ('' !== trim($block['description'])) {
            $descriptionNode = $dom->createElement('Description');
            $sequenceBlockNode->appendChild($descriptionNode);
            $descriptionNode->appendChild($dom->createTextNode($block['description']));
        }

        // add duration and/or start+end date
        $timingNode = $dom->createElement('Timing');
        $sequenceBlockNode->appendChild($timingNode);
        if ($block['duration']) {
            $durationNode = $dom->createElement('Duration');
            $timingNode->appendChild($durationNode);
            $durationNode->appendChild($dom->createTextNode('P' . $block['duration'] . 'D')); // duration in days.
        }

        if ($block['start_date']) {
            $datesNode = $dom->createElement('Dates');
            $timingNode->appendChild($datesNode);
            $startDateNode = $dom->createElement('StartDate', $block['start_date']);
            $datesNode->appendChild($startDateNode);
            $endDateNode = $dom->createElement('EndDate', $block['end_date']);
            $datesNode->appendChild($endDateNode);
        }



        // academic level
        $levelNode = $dom->createElement('Level', "/CurriculumInventory/AcademicLevels/Level[@number='{$block['academic_level_number']}']");
        $sequenceBlockNode->appendChild($levelNode);

        // clerkship type
        // map course clerkship type to "Clerkship Model"
        $clerkshipModel = false;
        switch ($block['course_clerkship_type_id']) {
            case Course_Clerkship_Type::INTEGRATED :
                $clerkshipModel = 'integrated';
                break;
            case Course_Clerkship_Type::BLOCK :
            case Course_Clerkship_Type::LONGITUDINAL :
                $clerkshipModel = 'rotation';
                break;
        }

        if ($clerkshipModel) {
            $clerkshipModelNode = $dom->createElement('ClerkshipModel', $clerkshipModel);
            $sequenceBlockNode->appendChild($clerkshipModelNode);
        }


        // competency object references
        if (array_key_exists('competency_object_references', $block)) {
            $domain = $inventory['report']['domain'];
            foreach ($block['competency_object_references']['program_objectives'] as $id) {
                $uri = $this->_createCompetencyObjectUri($domain, $id, 'program_objective');
                $this->_createCompetencyObjectReferenceNode($dom, $sequenceBlockNode, $uri);
            }
            foreach ($block['competency_object_references']['course_objectives'] as $id) {
                $uri = $this->_createCompetencyObjectUri($domain, $id, 'course_objective');
                $this->_createCompetencyObjectReferenceNode($dom, $sequenceBlockNode, $uri);
            }
        }
        // pre-conditions and post-conditions are n/a

        // event references
        if (array_key_exists('event_references', $block)) {
            foreach ($block['event_references'] as $reference) {
                $sequenceBlockEventNode = $dom->createElement('SequenceBlockEvent');
                $sequenceBlockNode->appendChild($sequenceBlockEventNode);
                if ($reference['required']) {
                    $sequenceBlockEventNode->setAttribute('required', 'true');
                } else {
                    $sequenceBlockEventNode->setAttribute('required', 'false');
                }
                $refUri = "/CurriculumInventory/Events/Event[@id='E{$reference['event_id']}']}";
                $eventReferenceNode = $dom->createElement('EventReferenceNode', $refUri);
                $sequenceBlockEventNode->appendChild($eventReferenceNode);

                // start/end-date
                // Not implemented at this point.
                //
                // Some food for thought:
                // This information may be retrieved from the date range values of offerings or independent learning
                // sessions associated with Ilios sessions.
                // E.g.
                // For a start date of this sequence block event reference, the earliest start date of any offerings
                // within a session may be assumed.
                // Likewise, the latest end date of any offerings within a session could be used for the end date of
                // event reference.
                // How accurate this will match the expected start/end date values here remains to be seen and will
                // require further discussion.
                // [ST 2013/08/08]
            }
        }

        // recursively generate XML for nested sequence blocks
        if (array_key_exists('children', $block)) {
            $order = 0;
            foreach ($block['children'] as $child) {
                // apply an incremental sort order for "ordered" sequence blocks
                // it is assumed that blocks already come pre-sorted
                if (Curriculum_Inventory_Sequence_Block::ORDERED == $block['child_sequence_order']) {
                    $order++;
                }
                $this->_createSequenceBlockNode($dom, $sequenceNode, $child, $inventory, $sequenceBlockNode, $order);
            }
        }
    }

    /**
     * Creates a "CompetencyObject" DOM node and populates it with given values, then appends it to the given parent node.
     * @param DomDocument $dom The document object.
     * @param DomElement $parentNode The parent node.
     * @param string $title The competency object's title.
     * @param string $uri An URI that uniquely identifies the competency object.
     * @param string $category One of 'program-level-competency', 'sequence-block-level-competency' or 'event-level-competency'.
     */
    protected function _createCompetencyObjectNode(DomDocument $dom, DomElement $parentNode, $title, $uri, $category)
    {
        $competencyObjectNode = $dom->createElement('CompetencyObject');
        $parentNode->appendChild($competencyObjectNode);
        $lomNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'lom');
        $competencyObjectNode->appendChild($lomNode);
        $lomGeneralNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'general');
        $lomNode->appendChild($lomGeneralNode);
        $lomIdentifierNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM','identifier');
        $lomGeneralNode->appendChild($lomIdentifierNode);
        $lomCatalogNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'catalog', 'URI');
        $lomIdentifierNode->appendChild($lomCatalogNode);
        $lomEntryNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM','title', $uri);
        $lomIdentifierNode->appendChild($lomEntryNode);
        $lomTitleNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'title');
        $lomGeneralNode->appendChild($lomTitleNode);
        $lomStringNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'string');
        $lomTitleNode->appendChild($lomStringNode);
        $lomStringNode->appendChild($dom->createTextNode(trim(strip_tags($title))));
        $categoryNode = $dom->createElement('Category');
        $competencyObjectNode->appendChild($categoryNode);
        $categoryNode->setAttribute('term', $category);
    }

    /**
     *
     * Creates a "CompetencyObjectReference" DOM node and populates it with given values, then appends it to the given parent node.
     * @param DomDocument $dom The document object.
     * @param DomElement $parentNode The parent node.
     * @param string $uri An URI that uniquely identifies the competency object.
     * @see Ilios_CurriculumInventory_Exporter::_createCompetencyObjectUri
     */
    protected function _createCompetencyObjectReferenceNode (DomDocument $dom, DomElement $parentNode, $uri)
    {
        //$uri = $this->_createCompetencyObjectUri($domain, $type, $id);
        $ref = "/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/lom:entry=\"{$uri}\"]";
        $competencyObjectReferenceNode = $dom->createElement('CompetencyObjectReference', $ref);
        $parentNode->appendChild($competencyObjectReferenceNode);
    }

    /**
     * @param DomDocument $dom
     * @param DomElement $parentNode
     * @param string $uri
     */
    protected function _createCompetencyFrameworkIncludesNode (DomDocument $dom, DomElement $parentNode, $uri)
    {
        $includesNode = $dom->createElement('Includes');
        $parentNode->appendChild($includesNode);
        $catalogNode = $dom->createElement('Catalog', 'URI');
        $includesNode->appendChild($catalogNode);
        $entryNode = $dom->createElement('Entry', $uri);
        $includesNode->appendChild($entryNode);
    }

    /**
     * @param DomDocument $dom
     * @param DomElement $parentNode
     * @param string $relUri1
     * @param string $relUri2
     * @param string $relationshipUri
     */
    protected function _createCompetencyFrameworkRelationNode (DomDocument $dom, DomElement $parentNode, $relUri1,
                                                               $relUri2, $relationshipUri)
    {
        $relationNode = $dom->createElement('Relation');
        $parentNode->appendChild($relationNode);
        $referenceNode = $dom->createElement('Reference1');
        $relationNode->appendChild($referenceNode);
        $catalogNode = $dom->createElement('Catalog', 'URI');
        $referenceNode->appendChild($catalogNode);
        $entryNode = $dom->createElement('Entry', $relUri1);
        $referenceNode->appendChild($entryNode);
        $relationshipNode = $dom->createElement('Relationship', $relationshipUri);
        $relationNode->appendChild($relationshipNode);
        $referenceNode = $dom->createElement('Reference2');
        $relationNode->appendChild($referenceNode);
        $catalogNode = $dom->createElement('Catalog', 'URI');
        $referenceNode->appendChild($catalogNode);
        $entryNode = $dom->createElement('Entry', $relUri2);
        $referenceNode->appendChild($entryNode);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function _createRelationshipUri($type)
    {
        return "http://www.w3.org/2004/02/skos/core#{$type}";
    }

    /**
     * Returns a URI that identifies a given competency object within the curriculum inventory.
     * Note: The returned URI is a bogus URL, but that's OK (for now).
     * @param string $domain The domain name of competency object's URI.
     * @param int $id The db record id of the competency object.
     * @param string $type the type of competency object. Must be one of
     *     "program_objective"
     *     "course_objective"
     *     "session_objective"
     * @return string The unique URI for the given competency object.
     */
    protected function _createCompetencyObjectUri ($domain, $id, $type)
    {
        return "http://{$domain}/{$type}/{$id}";
    }

    /**
     * Returns a URI that identifies a given PCRS as defined by the AAMC.
     * @param string $pcrsPartialUri A part of the URI that uniquely identifies te PCRS competency.
     * @return string The generated URI.
     */
    protected function _createPcrsUri ($pcrsPartialUri)
    {
        return "http://aamc.org/pcrs/{$pcrsPartialUri}";
    }
}
