<?php

namespace Ilios\CoreBundle\Service\CurriculumInventory;

use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Service\Config;

/**
 * Curriculum Inventory Exporter.
 *
 * Provides functionality for generating curriculum inventory reports and for exporting reports to XML according
 * to the MedBiquitous specification.
 *
 * @link http://www.medbiq.org/sites/default/files/files/CurriculumInventorySpecification.pdf
 * @link http://ns.medbiq.org/curriculuminventory/v1/curriculuminventory.xsd
 *
 */
class Exporter
{
    /**
     * @var CurriculumInventoryReportManager
     */
    protected $reportManager;

    /**
     * @var CurriculumInventoryInstitutionManager
     */
    protected $institutionManager;

    /** @var string */
    protected $supportingLink;

    /** @var string */
    protected $institutionDomain;

    /**
     * @param CurriculumInventoryReportManager $reportManager
     * @param CurriculumInventoryInstitutionManager $institutionManager
     * @param Config $config
     */
    public function __construct(
        CurriculumInventoryReportManager $reportManager,
        CurriculumInventoryInstitutionManager $institutionManager,
        Config $config
    ) {
        $this->reportManager = $reportManager;
        $this->institutionManager = $institutionManager;
        $this->institutionDomain = $config->get('institution_domain');
        $this->supportingLink = $config->get('supporting_link');
    }

    /**
     * Retrieves a curriculum inventory in a data structure that lends itself for an easy transformation into
     * XML-formatted report.
     *
     * @param CurriculumInventoryReportInterface $invReport The report object.
     * @return array An associated array, containing the inventory.
     *     Data is keyed off by:
     *         'report' ... The inventory report entity.
     *         'institution' ... An object representing the curriculum inventory's owning institution
     *         'events' ... An array of events, keyed off by event id. Each event is represented as assoc. array.
     *         'expectations' ... An associative array of arrays, each sub-array containing a
     *                            list of a different type of "competency object" within the curriculum.
     *                            These types are program objectives, course objectives and session objectives.
     *                            The keys for these type-specific sub-arrays are:
     *             'program_objectives'
     *             'course_objectives'
     *             'session_objectives'
     *             'framework' ... The competency framework data set.
     *                 'includes' ... Identifiers of the various competency objects referenced in the framework.
     *                     'pcrs_ids'
     *                     'program_objective_ids'
     *                     'course_objective_ids'
     *                     'session_objective_ids'
     *                 'relations' ... Relations between the various competencies within the framework
     *                     'program_objectives_to_pcrs'
     *                     'course_objectives_to_program_objectives'
     *                     'session_objectives_to_course_objectives'
     *         'sequence_block_references' ...relationships maps between sequence blocks and other curricular entities.
     *             'events' ... maps sequence blocks to events
     *             'competency_objects' .. maps sequence blocks to competency objects
     *
     * @throws \Exception
     */
    public function getCurriculumInventory(CurriculumInventoryReportInterface $invReport)
    {
        // report validation
        $program = $invReport->getProgram();
        if (! $program) {
            throw new \Exception('No program found for report with id  ' . $invReport->getId() . '.');
        }

        $school  = $program->getSchool();
        if (! $school) {
            throw new \Exception('No school found for program with id = ' . $program->getId() . '.');
        }

        /** @var CurriculumInventoryInstitutionInterface $institution */
        $institution = $this->institutionManager->findOneBy(['school' => $school->getId()]);
        if (! $institution) {
            throw new \Exception(
                'No curriculum inventory institution found for school with id = ' . $school->getId() . '.'
            );
        }

        $events = $this->reportManager->getEvents($invReport);
        $keywords = $this->reportManager->getEventKeywords($invReport);
        $resourceTypes = $this->reportManager->getEventResourceTypes($invReport);

        $eventRefsForSeqBlocks = $this->reportManager->getEventReferencesForSequenceBlocks($invReport);

        $programObjectives = $this->reportManager->getProgramObjectives($invReport);
        $sessionObjectives = $this->reportManager->getSessionObjectives($invReport);
        $courseObjectives = $this->reportManager->getCourseObjectives($invReport);

        $compObjRefsForSeqBlocks = $this->reportManager->getCompetencyObjectReferencesForSequenceBlocks($invReport);
        $compRefsForEvents = $this->reportManager->getCompetencyObjectReferencesForEvents($invReport);

        // The various objective type are all "Competency Objects" in the context of reporting the curriculum inventory.
        // The are grouped in the "Expectations" section of the report, lump 'em together here.
        $expectations =[];
        $expectations['program_objectives'] = $programObjectives;
        $expectations['session_objectives'] = $sessionObjectives;
        $expectations['course_objectives'] = $courseObjectives;


        // Build out the competency framework information and added to $expectations.
        $pcrs = $this->reportManager->getPcrs($invReport);

        $pcrsIds = array_keys($pcrs);
        $programObjectiveIds = array_keys($programObjectives);
        $courseObjectiveIds = array_keys($courseObjectives);
        $sessionObjectiveIds = array_keys($sessionObjectives);
        $includes = [
            'pcrs_ids' =>[],
            'program_objective_ids' => [],
            'course_objective_ids' => [],
            'session_objective_ids' => [],
        ];
        $relations = [
            'program_objectives_to_pcrs' => [],
            'course_objectives_to_program_objectives' => [],
            'session_objectives_to_course_objectives' => [],
        ];

        $rel = $this->reportManager->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds
        );
        $relations['program_objectives_to_pcrs'] = $rel['relations'];
        $includes['pcrs_ids'] = $rel['pcrs_ids'];
        $includes['program_objective_ids'] = $rel['program_objective_ids'];
        $rel = $this->reportManager->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds
        );
        $relations['course_objectives_to_program_objectives'] = $rel['relations'];
        $includes['program_objective_ids'] = array_values(
            array_unique(
                array_merge(
                    $includes['program_objective_ids'],
                    $rel['program_objective_ids']
                )
            )
        );
        $includes['course_objective_ids'] = $rel['course_objective_ids'];
        $rel = $this->reportManager->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );
        $relations['session_objectives_to_course_objectives'] = $rel['relations'];
        $includes['course_objective_ids'] = array_values(
            array_unique(
                array_merge(
                    $includes['course_objective_ids'],
                    $rel['course_objective_ids']
                )
            )
        );
        $includes['session_objective_ids'] = $rel['session_objective_ids'];

        $expectations['framework'] = [
            'includes' => $includes,
            'relations' => $relations,
        ];

        //
        // transmogrify inventory data for reporting and fill in the blanks
        //
        $events = $this->addKeywordsToEvents($events, $keywords);
        $events = $this->addResourceTypesToEvents($events, $resourceTypes);
        $events = $this->addCompetencyObjectReferencesToEvents($events, $compRefsForEvents);

        //
        // aggregate inventory into single return-array
        //
        $rhett =[];
        $rhett['report'] = $invReport;
        $rhett['expectations'] = $expectations;
        $rhett['institution'] = $institution;
        $rhett['events'] = $events;
        $rhett['sequence_block_references'] = [
            'events' => $eventRefsForSeqBlocks,
            'competency_objects' =>$compObjRefsForSeqBlocks,
        ];
        return $rhett;
    }

    /**
     * Creates an XML representation of the given curriculum inventory.
     * @param array $inventory An associative array representing the entire curriculum inventory.
     *     The inventory is expected to be structured like the output of <code>Exporter::getCurriculumInventory()<code>.
     * @return \DOMDocument The generated XML document.
     * @throws \DomException
     */
    public function createXmlReport(array $inventory)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $rootNode = $dom->createElementNS('http://ns.medbiq.org/curriculuminventory/v1/', 'CurriculumInventory');
        $rootNode->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $rootNode->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'schemaLocation',
            'http://ns.medbiq.org/curriculuminventory/v1/curriculuminventory.xsd'
        );
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:lom', 'http://ltsc.ieee.org/xsd/LOM');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://ns.medbiq.org/address/v1/');
        $rootNode->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cf',
            'http://ns.medbiq.org/competencyframework/v1/'
        );
        $rootNode->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:co',
            'http://ns.medbiq.org/competencyobject/v1/'
        );
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:hx', 'http://ns.medbiq.org/lom/extend/v1/');
        $rootNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:m', 'http://ns.medbiq.org/member/v1/');
        $dom->appendChild($rootNode);

        /** @var CurriculumInventoryReportInterface $report */
        $report = $inventory['report'];

        //
        // ReportID
        //
        $reportId = $report->getYear() . 'x' . $report->getProgram()->getId() . 'x' . $report->getId() . 'x' . time();
        $reportIdNode = $dom->createElement('ReportID', $reportId);
        $reportIdNode->setAttribute('domain', "idd:{$this->institutionDomain}:cireport");
        $rootNode->appendChild($reportIdNode);

        //
        // Institution
        //
        /** @var CurriculumInventoryInstitutionInterface $institution */
        $institution = $inventory['institution'];
        $institutionNode = $dom->createElement('Institution');
        $rootNode->appendChild($institutionNode);
        $institutionNameNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:InstitutionName');
        $institutionNameNode->appendChild($dom->createTextNode($institution->getName()));
        $institutionNode->appendChild($institutionNameNode);
        $institutionIdNode = $dom->createElementNS(
            'http://ns.medbiq.org/member/v1/',
            'm:InstitutionID',
            $institution->getAamcCode()
        );
        $institutionIdNode->setAttribute('domain', 'idd:aamc.org:institution');
        $institutionNode->appendChild($institutionIdNode);
        $addressNode = $dom->createElementNS('http://ns.medbiq.org/member/v1/', 'm:Address');
        $institutionNode->appendChild($addressNode);
        $streetAddressNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:StreetAddressLine');
        $streetAddressNode->appendChild($dom->createTextNode($institution->getAddressStreet()));
        $addressNode->appendChild($streetAddressNode);
        $cityNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:City', $institution->getAddressCity());
        $addressNode->appendChild($cityNode);
        $stateNode = $dom->createElementNS(
            'http://ns.medbiq.org/address/v1/',
            'a:StateOrProvince',
            $institution->getAddressStateOrProvince()
        );
        $addressNode->appendChild($stateNode);
        $zipcodeNode = $dom->createElementNS(
            'http://ns.medbiq.org/address/v1/',
            'a:PostalCode',
            $institution->getAddressZipcode()
        );
        $addressNode->appendChild($zipcodeNode);
        $countryNode = $dom->createElementNS('http://ns.medbiq.org/address/v1/', 'a:Country');
        $addressNode->appendChild($countryNode);
        $countryCodeNode = $dom->createElementNS(
            'http://ns.medbiq.org/address/v1/',
            'a:CountryCode',
            $institution->getAddressCountryCode()
        );
        $countryNode->appendChild($countryCodeNode);
        //
        // Program
        //
        /** @var ProgramInterface $program */
        $program = $report->getProgram();

        $programNode = $dom->createElement('Program');
        $rootNode->appendChild($programNode);
        $programNameNode = $dom->createElement('ProgramName');
        $programNameNode->appendChild($dom->createTextNode($program->getTitle()));
        $programNode->appendChild($programNameNode);
        $programIdNode = $dom->createElement('ProgramID', $program->getId());
        $programIdNode->setAttribute('domain', "idd:{$this->institutionDomain}:program");
        $programNode->appendChild($programIdNode);

        //
        // various other report attributes
        //
        $titleNode = $dom->createElement('Title');
        $titleNode->appendChild($dom->createTextNode($report->getName()));
        $rootNode->appendChild($titleNode);
        $reportDateNode = $dom->createElement('ReportDate', date('Y-m-d'));
        $rootNode->appendChild($reportDateNode);
        $reportingStartDateNode = $dom->createElement('ReportingStartDate', $report->getStartDate()->format('Y-m-d'));
        $rootNode->appendChild($reportingStartDateNode);
        $reportingEndDateNode = $dom->createElement('ReportingEndDate', $report->getEndDate()->format('Y-m-d'));
        $rootNode->appendChild($reportingEndDateNode);
        $languageNode = $dom->createElement('Language', 'en-US');
        $rootNode->appendChild($languageNode);
        $descriptionNode = $dom->createElement('Description');
        $descriptionNode->appendChild($dom->createTextNode($report->getDescription()));
        $rootNode->appendChild($descriptionNode);

        if ($this->supportingLink) {
            $supportingLinkNode = $dom->createElement('SupportingLink', $this->supportingLink);
            $rootNode->appendChild($supportingLinkNode);
        }
        //
        // Events
        //
        $eventsNode = $dom->createElement('Events');
        $rootNode->appendChild($eventsNode);
        foreach ($inventory['events'] as $event) {
            $eventNode = $dom->createElement('Event');
            $eventsNode->appendChild($eventNode);
            $eventNode->setAttribute('id', 'E' . $event['event_id']);
            $eventTitleNode = $dom->createElement('Title');
            $eventNode->appendChild($eventTitleNode);
            $eventTitleNode->appendChild($dom->createTextNode($event['title']));
            $duration = str_pad($event['duration'], 2, '0', STR_PAD_LEFT);
            $eventDurationNode = $dom->createElement('EventDuration', 'PT' . $duration . 'M');
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
                    $uri = $this->createCompetencyObjectUri($id, 'program_objective');
                    $this->createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
                foreach ($event['competency_object_references']['course_objectives'] as $id) {
                    $uri = $this->createCompetencyObjectUri($id, 'course_objective');
                    $this->createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
                foreach ($event['competency_object_references']['session_objectives'] as $id) {
                    $uri = $this->createCompetencyObjectUri($id, 'session_objective');
                    $this->createCompetencyObjectReferenceNode($dom, $eventNode, $uri);
                }
            }

            // resource types
            if (array_key_exists('resource_types', $event)) {
                foreach ($event['resource_types'] as $resourceType) {
                    $resourceTypeNode = $dom->createElement('ResourceType');
                    $eventNode->appendChild($resourceTypeNode);
                    $resourceTypeNode->setAttribute('sourceID', $resourceType['resource_type_id']);
                    $resourceTypeNode->appendChild($dom->createTextNode($resourceType['resource_type_title']));
                }
            }

            // instructional- or assessment-method
            //
            // NOTE: unmapped session types to AAMC methods will result in empty values in the
            // <InstructionalMethod> or <AssessmentMethod> elements.
            // Which will result the report being rejected on import.
            // The alternatives would have been to:
            // (a) exclude events with unknown AAMC methods.
            // (b) raise an exception on report generation.
            // Neither of which are IMO preferable to the current approach
            // to "kick the bucket down the road" at this point.
            // Option (b) may be something could be implemented at a later point,
            // once the AAMC's CI tool and business rules
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
                    case 'formative':
                        $assessmentMethodNode->setAttribute('purpose', 'Formative');
                        break;
                    case 'summative':
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
        $expectations = $inventory['expectations'];
        $expectationsNode = $dom->createElement('Expectations');
        $rootNode->appendChild($expectationsNode);
        // program objectives
        foreach ($expectations['program_objectives'] as $programObjective) {
            $uri = $this->createCompetencyObjectUri($programObjective['objective_id'], 'program_objective');
            $this->createCompetencyObjectNode(
                $dom,
                $expectationsNode,
                $programObjective['title'],
                $uri,
                'program-level-competency'
            );
        }
        // course objectives
        foreach ($expectations['course_objectives'] as $courseObjective) {
            $uri = $this->createCompetencyObjectUri($courseObjective['objective_id'], 'course_objective');
            $this->createCompetencyObjectNode(
                $dom,
                $expectationsNode,
                $courseObjective['title'],
                $uri,
                'sequence-block-level-competency'
            );
        }
        // session objectives
        foreach ($expectations['session_objectives'] as $sessionObjective) {
            $uri = $this->createCompetencyObjectUri($sessionObjective['objective_id'], 'session_objective');
            $this->createCompetencyObjectNode(
                $dom,
                $expectationsNode,
                $sessionObjective['title'],
                $uri,
                'event-level-competency'
            );
        }
        // add competency framework
        $this->createCompetencyFrameworkNode($dom, $expectationsNode, $report, $reportId, $expectations);

        //
        // Academic Levels
        //
        $levels = $report->getAcademicLevels()->filter(function (CurriculumInventoryAcademicLevelInterface $level) {
            return $level->getSequenceBlocks()->count() > 0;
        });

        $academicLevelsNode = $dom->createElement('AcademicLevels');
        $rootNode->appendChild($academicLevelsNode);
        $levelsInProgramNode = $dom->createElement('LevelsInProgram', $levels->count());
        $academicLevelsNode->appendChild($levelsInProgramNode);
        $iterator = $levels->getIterator();
        /** @var CurriculumInventoryAcademicLevelInterface $level */
        foreach ($iterator as $level) {
            $levelNode = $dom->createElement('Level');
            $academicLevelsNode->appendChild($levelNode);
            $levelNode->setAttribute('number', $level->getLevel());
            $labelNode = $dom->createElement('Label');
            $levelNode->appendChild($labelNode);
            $labelNode->appendChild($dom->createTextNode($level->getName()));
            if ('' !== trim($level->getDescription())) {
                $descriptionNode = $dom->createElement('Description');
                $levelNode->appendChild($descriptionNode);
                $descriptionNode->appendChild($dom->createTextNode($level->getDescription()));
            }
        }

        //
        // Sequence
        //
        $sequence = $report->getSequence();
        $sequenceNode = $dom->createElement('Sequence');

        $rootNode->appendChild($sequenceNode);
        if ('' !== trim($sequence->getDescription())) {
            $sequenceDescriptionNode = $dom->createElement('Description');
            $sequenceNode->appendChild($sequenceDescriptionNode);
            $sequenceDescriptionNode->appendChild($dom->createTextNode($sequence->getDescription()));
        }

        //
        // Sequence Blocks
        //
        $sequenceBlocks = $report->getSequenceBlocks();
        $topLevelSequenceBlocks = $sequenceBlocks->filter(function (CurriculumInventorySequenceBlockInterface $block) {
            $parent = $block->getParent();
            return empty($parent);
        })->toArray();
        usort(
            $topLevelSequenceBlocks,
            [
                'Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock',
                'compareSequenceBlocksWithDefaultStrategy'
            ]
        );
        foreach ($topLevelSequenceBlocks as $block) {
            $this->createSequenceBlockNode(
                $dom,
                $sequenceNode,
                $block,
                $inventory['sequence_block_references']['events'],
                $inventory['sequence_block_references']['competency_objects']
            );
        }

        //
        // Integration - currently not supported
        //
        return $dom;
    }

    /**
     * Loads the curriculum inventory for a given report and exports it as XML document.
     * @param CurriculumInventoryReportInterface $report The report.
     * @return \DomDocument The fully populated report.
     * @throws \DomException
     * @throws \Exception
     * @see Ilios_CurriculumInventory_Exporter::getCurriculumInventory()
     * @see Ilios_CurriculumInventory_Exporter::createXmlReport()
     */
    public function getXmlReport(CurriculumInventoryReportInterface $report)
    {
        $inventory = $this->getCurriculumInventory($report);
        return $this->createXmlReport($inventory);
    }

    /**
     * Adds keywords to events.
     * @param array $events A list of events.
     * @param array $keywords A list of keywords.
     * @return array The events with the keywords added.
     */
    protected function addKeywordsToEvents(array $events, array $keywords)
    {
        foreach ($keywords as $keyword) {
            $eventId = $keyword['event_id'];
            if (! array_key_exists($eventId, $events)) {
                continue;
            }
            if (! array_key_exists('keywords', $events[$eventId])) {
                $events[$eventId]['keywords'] =[];
            }
            $events[$eventId]['keywords'][] = $keyword;
        }
        return $events;
    }

    /**
     * Adds AAMC resource types to events.
     * @param array $events A list of events.
     * @param array $resourceTypes A list of resource types.
     * @return array The events with their resource types added.
     */
    protected function addResourceTypesToEvents(array $events, array $resourceTypes)
    {
        foreach ($resourceTypes as $resourceType) {
            $eventId = $resourceType['event_id'];
            if (! array_key_exists($eventId, $events)) {
                continue;
            }
            if (! array_key_exists('resource_types', $events[$eventId])) {
                $events[$eventId]['resource_types'] =[];
            }
            $events[$eventId]['resource_types'][] = $resourceType;
        }
        return $events;
    }

    /**
     * Adds competency objects references to events.
     * @param array $events A list of events.
     * @param array $references A list of competency object references.
     * @return array The events with references added.
     */
    protected function addCompetencyObjectReferencesToEvents(array $events, array $references)
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
     * Creates the competency framework node and child-nodes, and adds them to a given parent node (<Expectations>).
     *
     * @param \DOMDocument $dom
     * @param \DOMElement $parentNode
     * @param CurriculumInventoryReportInterface $report
     * @param string $reportId
     * @param array $expectations
     */
    protected function createCompetencyFrameworkNode(
        \DOMDocument $dom,
        \DOMElement $parentNode,
        CurriculumInventoryReportInterface $report,
        $reportId,
        array $expectations
    ) {
        // competency framework
        $competencyFrameworkNode = $dom->createElement('CompetencyFramework');
        $parentNode->appendChild($competencyFrameworkNode);

        // lom
        $lomNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'lom');
        $competencyFrameworkNode->appendChild($lomNode);
        $lomGeneralNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'general');
        $lomNode->appendChild($lomGeneralNode);
        $lomIdentifierNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'identifier');
        $lomGeneralNode->appendChild($lomIdentifierNode);
        $lomCatalogNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'catalog', 'URI');
        $lomIdentifierNode->appendChild($lomCatalogNode);
        $frameworkUri = "http://{$this->institutionDomain}/competency_framework/{$reportId}";
        $lomEntryNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'entry', $frameworkUri);
        $lomIdentifierNode->appendChild($lomEntryNode);
        $lomTitleNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'title');
        $lomGeneralNode->appendChild($lomTitleNode);
        $lomStringNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'string');
        $lomTitleNode->appendChild($lomStringNode);
        $title = 'Competency Framework for ' . $report->getName();
        $lomStringNode->appendChild($dom->createTextNode($title));

        // includes
        $competencyIds = $expectations['framework']['includes']['pcrs_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createPcrsUri($id);
            $this->createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['program_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'program_objective');
            $this->createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['course_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'course_objective');
            $this->createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['session_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'session_objective');
            $this->createCompetencyFrameworkIncludesNode($dom, $competencyFrameworkNode, $uri);
        }
        // relations
        $relations = $expectations['framework']['relations']['program_objectives_to_pcrs'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri($relation['rel1'], 'program_objective');
            $relUri2 = $this->createPcrsUri($relation['rel2']);
            $relationshipUri = $this->createRelationshipUri('related');
            $this->createCompetencyFrameworkRelationNode(
                $dom,
                $competencyFrameworkNode,
                $relUri2,
                $relUri1,
                $relationshipUri
            );
        }
        $relations = $expectations['framework']['relations']['course_objectives_to_program_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri($relation['rel1'], 'program_objective');
            $relUri2 = $this->createCompetencyObjectUri($relation['rel2'], 'course_objective');
            $relationshipUri = $this->createRelationshipUri('narrower');
            $this->createCompetencyFrameworkRelationNode(
                $dom,
                $competencyFrameworkNode,
                $relUri1,
                $relUri2,
                $relationshipUri
            );
        }
        $relations = $expectations['framework']['relations']['session_objectives_to_course_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri($relation['rel1'], 'course_objective');
            $relUri2 = $this->createCompetencyObjectUri($relation['rel2'], 'session_objective');
            $relationshipUri = $this->createRelationshipUri('narrower');
            $this->createCompetencyFrameworkRelationNode(
                $dom,
                $competencyFrameworkNode,
                $relUri1,
                $relUri2,
                $relationshipUri
            );
        }
    }

    /**
     * Recursively creates and appends sequence block nodes to the XML document.
     *
     * @param \DOMDocument $dom the document object
     * @param \DOMElement $sequenceNode the sequence DOM node to append to
     * @param CurriculumInventorySequenceBlockInterface $block the current sequence block
     * @param array $eventReferences A reference map of sequence blocks to events.
     * @param array $competencyObjectReferences A reference map of sequence blocks to competency objects.
     * @param \DOMElement|null $parentSequenceBlockNode the DOM node representing the parent sequence block.
     * @param int $order of this sequence block in relation to other nested sequence blocks. '0' if n/a.
     */
    protected function createSequenceBlockNode(
        \DOMDocument $dom,
        \DOMElement $sequenceNode,
        CurriculumInventorySequenceBlockInterface $block,
        array $eventReferences,
        array $competencyObjectReferences,
        \DOMElement $parentSequenceBlockNode = null,
        $order = 0
    ) {
        $sequenceBlockNode = $dom->createElement('SequenceBlock');
        $sequenceNode->appendChild($sequenceBlockNode);
        // append a reference to _this_ sequence block to the parent sequence block
        if (isset($parentSequenceBlockNode)) {
            $ref = "/CurriculumInventory/Sequence/SequenceBlock[@id='{$block->getId()}']";
            $sequenceBlockReferenceNode = $dom->createElement('SequenceBlockReference', $ref);
            $parentSequenceBlockNode->appendChild($sequenceBlockReferenceNode);
            if ($order) {
                $sequenceBlockReferenceNode->setAttribute('order', $order);
            }
        }
        $sequenceBlockNode->setAttribute('id', $block->getId());
        switch ($block->getRequired()) {
            case CurriculumInventorySequenceBlockInterface::OPTIONAL:
                $sequenceBlockNode->setAttribute('required', 'Optional');
                break;
            case CurriculumInventorySequenceBlockInterface::REQUIRED:
                $sequenceBlockNode->setAttribute('required', 'Required');
                break;
            case CurriculumInventorySequenceBlockInterface::REQUIRED_IN_TRACK:
                $sequenceBlockNode->setAttribute('required', 'Required In Track');
                break;
        }
        switch ($block->getChildSequenceOrder()) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                $sequenceBlockNode->setAttribute('order', 'Ordered');
                break;
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
                $sequenceBlockNode->setAttribute('order', 'Unordered');
                break;
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
                $sequenceBlockNode->setAttribute('order', 'Parallel');
                break;
        }

        $min = $block->getMinimum();
        if ($min) {
            $sequenceBlockNode->setAttribute('minimum', $min);
        }

        $max = $block->getMaximum();
        if ($max) {
            $sequenceBlockNode->setAttribute('maximum', $max);
        }

        if ($block->hasTrack()) {
            $sequenceBlockNode->setAttribute('track', 'true');
        } else {
            $sequenceBlockNode->setAttribute('track', 'false');
        }

        $titleNode = $dom->createElement('Title');
        $sequenceBlockNode->appendChild($titleNode);
        $titleNode->appendChild($dom->createTextNode($block->getTitle()));

        if ('' !== trim($block->getDescription())) {
            $descriptionNode = $dom->createElement('Description');
            $sequenceBlockNode->appendChild($descriptionNode);
            $descriptionNode->appendChild($dom->createTextNode($block->getDescription()));
        }

        // add duration and/or start+end date
        $timingNode = $dom->createElement('Timing');
        $sequenceBlockNode->appendChild($timingNode);
        if ($block->getDuration()) {
            $durationNode = $dom->createElement('Duration');
            $timingNode->appendChild($durationNode);
            $durationNode->appendChild($dom->createTextNode('P' . $block->getDuration() . 'D')); // duration in days.
        }

        if ($block->getStartDate()) {
            $datesNode = $dom->createElement('Dates');
            $timingNode->appendChild($datesNode);
            $startDateNode = $dom->createElement('StartDate', $block->getStartDate()->format('Y-m-d'));
            $datesNode->appendChild($startDateNode);
            $endDateNode = $dom->createElement('EndDate', $block->getEndDate()->format('Y-m-d'));
            $datesNode->appendChild($endDateNode);
        }



        // academic level
        $levelNode = $dom->createElement(
            'Level',
            "/CurriculumInventory/AcademicLevels/Level[@number='{$block->getAcademicLevel()->getLevel()}']"
        );
        $sequenceBlockNode->appendChild($levelNode);

        // clerkship type
        // map course clerkship type to "Clerkship Model"
        // @todo Refactor this out into utility method. [ST 2015/09/14]
        $course = $block->getCourse();
        $clerkshipModel = false;
        if ($course) {
            $clerkshipType = $course->getClerkshipType() ? $course->getClerkshipType()->getId() : null;
            switch ($clerkshipType) {
                case CourseClerkshipTypeInterface::INTEGRATED:
                    $clerkshipModel = 'integrated';
                    break;
                case CourseClerkshipTypeInterface::BLOCK:
                case CourseClerkshipTypeInterface::LONGITUDINAL:
                    $clerkshipModel = 'rotation';
                    break;
            }
        }
        if ($clerkshipModel) {
            $clerkshipModelNode = $dom->createElement('ClerkshipModel', $clerkshipModel);
            $sequenceBlockNode->appendChild($clerkshipModelNode);
        }

        // link to competency objects
        if (array_key_exists($block->getId(), $competencyObjectReferences)) {
            $refs  = $competencyObjectReferences[$block->getId()];
            foreach ($refs['program_objectives'] as $id) {
                $uri = $this->createCompetencyObjectUri($id, 'program_objective');
                $this->createCompetencyObjectReferenceNode($dom, $sequenceBlockNode, $uri);
            }
            foreach ($refs['course_objectives'] as $id) {
                $uri = $this->createCompetencyObjectUri($id, 'course_objective');
                $this->createCompetencyObjectReferenceNode($dom, $sequenceBlockNode, $uri);
            }
        }
        // pre-conditions and post-conditions are n/a

        // link to events
        if (array_key_exists($block->getId(), $eventReferences)) {
            $refs = $eventReferences[$block->getId()];
            foreach ($refs as $reference) {
                $sequenceBlockEventNode = $dom->createElement('SequenceBlockEvent');
                $sequenceBlockNode->appendChild($sequenceBlockEventNode);
                if ($reference['required']) {
                    $sequenceBlockEventNode->setAttribute('required', 'true');
                } else {
                    $sequenceBlockEventNode->setAttribute('required', 'false');
                }
                $refUri = "/CurriculumInventory/Events/Event[@id='E{$reference['event_id']}']";
                $eventReferenceNode = $dom->createElement('EventReference', $refUri);
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
        $children = $block->getChildrenAsSortedList();
        if (! empty($children)) {
            $order = 0;
            $isOrdered = CurriculumInventorySequenceBlockInterface::ORDERED
                === $block->getChildSequenceOrder();
            foreach ($children as $child) {
                // apply an incremental sort order for "ordered" sequence blocks
                if ($isOrdered) {
                    $order++;
                }
                $this->createSequenceBlockNode(
                    $dom,
                    $sequenceNode,
                    $child,
                    $eventReferences,
                    $competencyObjectReferences,
                    $sequenceBlockNode,
                    $order
                );
            }
        }
    }

    /**
     * Creates a "CompetencyObject" DOM node and populates it with given values,
     * then appends it to the given parent node.
     *
     * @param \DOMDocument $dom The document object.
     * @param \DOMElement $parentNode The parent node.
     * @param string $title The competency object's title.
     * @param string $uri An URI that uniquely identifies the competency object.
     * @param string $category 'program-level-competency', 'sequence-block-level-competency or 'event-level-competency'.
     */
    protected function createCompetencyObjectNode(\DOMDocument $dom, \DOMElement $parentNode, $title, $uri, $category)
    {
        $competencyObjectNode = $dom->createElement('CompetencyObject');
        $parentNode->appendChild($competencyObjectNode);
        $lomNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'lom');
        $competencyObjectNode->appendChild($lomNode);
        $lomGeneralNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'general');
        $lomNode->appendChild($lomGeneralNode);
        $lomIdentifierNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'identifier');
        $lomGeneralNode->appendChild($lomIdentifierNode);
        $lomCatalogNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'catalog', 'URI');
        $lomIdentifierNode->appendChild($lomCatalogNode);
        $lomEntryNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'entry', $uri);
        $lomIdentifierNode->appendChild($lomEntryNode);
        $lomTitleNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'title');
        $lomGeneralNode->appendChild($lomTitleNode);
        $lomStringNode = $dom->createElementNS('http://ltsc.ieee.org/xsd/LOM', 'string');
        $lomTitleNode->appendChild($lomStringNode);
        $lomStringNode->appendChild($dom->createTextNode(trim(strip_tags($title))));
        $categoryNode = $dom->createElement('co:Category');
        $competencyObjectNode->appendChild($categoryNode);
        $categoryNode->setAttribute('term', $category);
    }

    /**
     *
     * Creates a "CompetencyObjectReference" DOM node and populates it with given values,
     * then appends it to the given parent node.
     *
     * @param \DOMDocument $dom The document object.
     * @param \DOMElement $parentNode The parent node.
     * @param string $uri An URI that uniquely identifies the competency object.
     * @see Ilios_CurriculumInventory_Exporter::_createCompetencyObjectUri
     */
    protected function createCompetencyObjectReferenceNode(\DOMDocument $dom, \DOMElement $parentNode, $uri)
    {
        $ref =
            "/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/lom:entry='{$uri}']";
        $competencyObjectReferenceNode = $dom->createElement('CompetencyObjectReference', $ref);
        $parentNode->appendChild($competencyObjectReferenceNode);
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMElement $parentNode
     * @param string $uri
     */
    protected function createCompetencyFrameworkIncludesNode(\DOMDocument $dom, \DOMElement $parentNode, $uri)
    {
        $includesNode = $dom->createElementNS('http://ns.medbiq.org/competencyframework/v1/', 'cf:Includes');
        $parentNode->appendChild($includesNode);
        $catalogNode = $dom->createElementNS('http://ns.medbiq.org/competencyframework/v1/', 'cf:Catalog', 'URI');
        $includesNode->appendChild($catalogNode);
        $entryNode = $dom->createElementNS('http://ns.medbiq.org/competencyframework/v1/', 'cf:Entry', $uri);
        $includesNode->appendChild($entryNode);
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMElement $parentNode
     * @param string $relUri1
     * @param string $relUri2
     * @param string $relationshipUri
     */
    protected function createCompetencyFrameworkRelationNode(
        \DOMDocument $dom,
        \DOMElement $parentNode,
        $relUri1,
        $relUri2,
        $relationshipUri
    ) {
        $relationNode = $dom->createElement('cf:Relation');
        $parentNode->appendChild($relationNode);
        $referenceNode = $dom->createElement('cf:Reference1');
        $relationNode->appendChild($referenceNode);
        $catalogNode = $dom->createElement('cf:Catalog', 'URI');
        $referenceNode->appendChild($catalogNode);
        $entryNode = $dom->createElement('cf:Entry', $relUri1);
        $referenceNode->appendChild($entryNode);
        $relationshipNode = $dom->createElement('cf:Relationship', $relationshipUri);
        $relationNode->appendChild($relationshipNode);
        $referenceNode = $dom->createElement('cf:Reference2');
        $relationNode->appendChild($referenceNode);
        $catalogNode = $dom->createElement('cf:Catalog', 'URI');
        $referenceNode->appendChild($catalogNode);
        $entryNode = $dom->createElement('cf:Entry', $relUri2);
        $referenceNode->appendChild($entryNode);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function createRelationshipUri($type)
    {
        return "http://www.w3.org/2004/02/skos/core#{$type}";
    }

    /**
     * Returns a URI that identifies a given competency object within the curriculum inventory.
     * Note: The returned URI is a bogus URL, but that's OK (for now).
     * @param int $id The db record id of the competency object.
     * @param string $type the type of competency object. Must be one of
     *     "program_objective"
     *     "course_objective"
     *     "session_objective"
     * @return string The unique URI for the given competency object.
     */
    protected function createCompetencyObjectUri($id, $type)
    {
        return "http://{$this->institutionDomain}/{$type}/{$id}";
    }

    /**
     * Returns a URI that identifies a given PCRS as defined by the AAMC.
     * @param string $pcrsPartialUri A part of the URI that uniquely identifies te PCRS competency.
     * @return string The generated URI.
     */
    protected function createPcrsUri($pcrsPartialUri)
    {
        return "https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#{$pcrsPartialUri}";
    }
}
