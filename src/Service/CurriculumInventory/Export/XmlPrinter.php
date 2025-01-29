<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory\Export;

use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CourseClerkshipTypeInterface;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryInstitutionInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use Exception;
use XMLWriter;

/**
 * XML printer for Curriculum Inventory reporting.
 *
 * @package App\Service\CurriculumInventory\Export
 */
class XmlPrinter
{
    public const string CATEGORY_TERM_PROGRAM_LEVEL_COMPETENCY = 'program-level-competency';
    public const string CATEGORY_TERM_PROGRAM_OBJECTIVE_DOMAIN = 'program-objective-domain';
    public const string CATEGORY_TERM_SEQUENCE_BLOCK_LEVEL_COMPETENCY = 'sequence-block-level-competency';
    public const string CATEGORY_TERM_EVENT_LEVEL_COMPETENCY = 'event-level-competency';
    /**
     * Creates an XML representation of the given curriculum inventory.
     * @param array $inventory An associated array, containing the inventory.
     *     Data is keyed off by:
     *         'report' ... The inventory report entity.
     *         'created_at' ... UNIX timestamp indicating when this report was created.
     *         'supporting_link' ... A link to supporting information of the curriculum.
     *         'institution_domain' ... URN part of the report id.
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
     * @throws Exception
     */
    public function print(array $inventory): string
    {
        $xw = new XMLWriter();
        $xw->openMemory();
        $xw->setIndent(true);
        $xw->setIndentString('  ');
        $xw->startDocument('1.0', 'UTF-8');
        $xw->startElementNs(null, 'CurriculumInventory', 'http://ns.medbiq.org/curriculuminventory/v10/');
        $xw->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xw->writeAttribute(
            'xsi:schemaLocation',
            'http://ns.medbiq.org/curriculuminventory/v10/'
            . ' http://ns.medbiq.org/curriculuminventory/v10/curriculuminventory.xsd'
        );
        $xw->writeAttribute('xmlns:lom', 'http://ltsc.ieee.org/xsd/LOM');
        $xw->writeAttribute('xmlns:a', 'http://ns.medbiq.org/address/v1/');
        $xw->writeAttribute('xmlns:cf', 'http://ns.medbiq.org/competencyframework/v1/');
        $xw->writeAttribute('xmlns:co', 'http://ns.medbiq.org/competencyobject/v1/');
        $xw->writeAttribute('xmlns:hx', 'http://ns.medbiq.org/lom/extend/v1/');
        $xw->writeAttribute('xmlns:m', 'http://ns.medbiq.org/member/v1/');

        /** @var CurriculumInventoryReportInterface $report */
        $report = $inventory['report'];

        $institutionDomain = $inventory['institution_domain'];

        //
        // ReportID
        //
        $reportId = $report->getYear() . 'x' . $report->getProgram()->getId() . 'x' .
            $report->getId() . 'x' . $inventory['created_at'];
        $xw->startElement('ReportID');
        $xw->writeAttribute('domain', "idd:{$institutionDomain}:cireport");
        $xw->text($reportId);
        $xw->endElement(); // </ReportID>

        //
        // Institution
        //
        /** @var CurriculumInventoryInstitutionInterface $institution */
        $institution = $inventory['institution'];
        $xw->startElement('Institution');
        $xw->writeElement('m:InstitutionName', $institution->getName());
        $xw->startElement('m:InstitutionID');
        $xw->writeAttribute('domain', 'idd:aamc.org:institution');
        $xw->text($institution->getAamcCode());
        $xw->endElement(); // </m:InstitutionID>
        $xw->startElement('m:Address');
        $xw->writeElement('a:StreetAddressLine', $institution->getAddressStreet());
        $xw->writeElement('a:City', $institution->getAddressCity());
        $xw->writeElement('a:StateOrProvince', $institution->getAddressStateOrProvince());
        $xw->writeElement('a:PostalCode', $institution->getAddressZipcode());
        $xw->startElement('a:Country');
        $xw->writeElement('a:CountryCode', $institution->getAddressCountryCode());
        $xw->endElement(); // </a:Country>
        $xw->endElement(); // </m:Address>
        $xw->endElement(); // </Institution>

        //
        // Program
        //
        $program = $report->getProgram();
        $xw->startElement('Program');
        $xw->writeElement('ProgramName', $program->getTitle());
        $xw->startElement('ProgramID');
        $xw->writeAttribute('domain', "idd:{$institutionDomain}:program");
        $xw->text((string) $program->getId());
        $xw->endElement(); // </ProgramID>,
        $xw->endElement(); // </Program>

        //
        // various other report attributes
        //
        $xw->writeElement('Title', $report->getName());
        $xw->writeElement('ReportDate', date('Y-m-d'));
        $xw->writeElement('ReportingStartDate', $report->getStartDate()->format('Y-m-d'));
        $xw->writeElement('ReportingEndDate', $report->getEndDate()->format('Y-m-d'));
        $xw->writeElement('Language', 'en-US');
        $xw->writeElement('Description', $report->getDescription() ?: '');
        if ($inventory['supporting_link']) {
            $xw->writeElement('SupportingLink', $inventory['supporting_link']);
        }

        //
        // Events
        //
        $xw->startElement('Events');
        foreach ($inventory['events'] as $event) {
            $xw->startElement('Event');
            $xw->writeAttribute('id', 'E' . $event['event_id']);
            $xw->writeElement('Title', $event['title']);
            $duration = 'PT' . str_pad((string)$event['duration'], 2, '0', STR_PAD_LEFT) . 'M';
            $xw->writeElement('EventDuration', $duration);
            if (is_string($event['description']) && '' !== trim($event['description'])) {
                $xw->writeElement('Description', (trim(strip_tags($event['description']))));
            }
            // keywords
            if (array_key_exists('keywords', $event)) {
                foreach ($event['keywords'] as $keyword) {
                    $xw->startElement('Keyword');
                    $xw->writeAttribute('hx:source', $keyword['source']);
                    $xw->writeAttribute('hx:id', (string) $keyword['id']);
                    $xw->writeElement('hx:string', $keyword['name']);
                    $xw->endElement(); // </Keyword>
                }
            }
            // competency object references
            if (array_key_exists('competency_object_references', $event)) {
                foreach ($event['competency_object_references']['program_objectives'] as $id) {
                    $uri = $this->createCompetencyObjectUri($id, 'program_objective', $institutionDomain);
                    $this->writeCompetencyObjectReferenceNode($xw, $uri);
                }
                foreach ($event['competency_object_references']['course_objectives'] as $id) {
                    $uri = $this->createCompetencyObjectUri($id, 'course_objective', $institutionDomain);
                    $this->writeCompetencyObjectReferenceNode($xw, $uri);
                }
                foreach ($event['competency_object_references']['session_objectives'] as $id) {
                    $uri = $this->createCompetencyObjectUri($id, 'session_objective', $institutionDomain);
                    $this->writeCompetencyObjectReferenceNode($xw, $uri);
                }
            }
            // resource types
            if (array_key_exists('resource_types', $event)) {
                foreach ($event['resource_types'] as $resourceType) {
                    $xw->writeElement('ResourceType', $resourceType['resource_type_id']);
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
                $xw->startElement('AssessmentMethod');
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
                        $xw->writeAttribute('purpose', 'Formative');
                        break;
                    case 'summative':
                    default:
                        $xw->writeAttribute('purpose', 'Summative');
                }
                $xw->text((string) $event['method_id']);
                $xw->endElement(); // </AssessmentMethod>
            } else {
                $xw->startElement('InstructionalMethod');
                $xw->writeAttribute('primary', 'true');
                // here, we add the event's duration, in full, as instructional method duration.
                // we can get away with this since there's currently only one instructional method
                // at the most associated with any given event.
                $xw->writeAttribute('instructionalMethodDuration', $duration);
                $xw->text((string) $event['method_id']);
                $xw->endElement(); // </InstructionalMethod>
            }
            $xw->endElement(); // </Event>
        }
        $xw->endElement(); // </Events>

        //
        // Expectations
        //
        $expectations = $inventory['expectations'];
        $xw->startElement('Expectations');
        // program objectives
        foreach ($expectations['program_objectives'] as $programObjective) {
            $uri = $this->createCompetencyObjectUri(
                $programObjective['id'],
                'program_objective',
                $institutionDomain
            );
            $this->writeCompetencyObjectNode(
                $xw,
                $programObjective['title'],
                $uri,
                self::CATEGORY_TERM_PROGRAM_LEVEL_COMPETENCY
            );
        }
        // course objectives
        foreach ($expectations['course_objectives'] as $courseObjective) {
            $uri = $this->createCompetencyObjectUri(
                $courseObjective['id'],
                'course_objective',
                $institutionDomain
            );
            $this->writeCompetencyObjectNode(
                $xw,
                $courseObjective['title'],
                $uri,
                self::CATEGORY_TERM_SEQUENCE_BLOCK_LEVEL_COMPETENCY
            );
        }
        // session objectives
        foreach ($expectations['session_objectives'] as $sessionObjective) {
            $uri = $this->createCompetencyObjectUri(
                $sessionObjective['id'],
                'session_objective',
                $institutionDomain
            );
            $this->writeCompetencyObjectNode(
                $xw,
                $sessionObjective['title'],
                $uri,
                self::CATEGORY_TERM_EVENT_LEVEL_COMPETENCY
            );
        }
        // add competency framework
        $this->writeCompetencyFrameworkNode($xw, $report, $reportId, $institutionDomain, $expectations);
        $xw->endElement(); // </Expectations>

        //
        // Academic Levels
        //
        $levels = $report->getAcademicLevels()->filter(
            fn(CurriculumInventoryAcademicLevelInterface $level)
            => $level->getStartingSequenceBlocks()->count() > 0 || $level->getEndingSequenceBlocks()->count() > 0
        );

        $xw->startElement('AcademicLevels');
        $xw->writeElement('LevelsInProgram', (string) $levels->count());
        $iterator = $levels->getIterator();
        /** @var CurriculumInventoryAcademicLevelInterface $level */
        foreach ($iterator as $level) {
            $xw->startElement('Level');
            $xw->writeAttribute('number', (string) $level->getLevel());
            $xw->writeElement('Label', $level->getName());
            $description = $level->getDescription();
            if (is_string($description) && '' !== trim($description)) {
                $xw->writeElement('Description', $level->getDescription());
            }
            $xw->endElement(); // </Level>
        }
        $xw->endElement(); // </AcademicLevels>

        //
        // Sequence
        //
        $sequence = $report->getSequence();
        $xw->startElement('Sequence');
        $description = $sequence->getDescription();
        if (is_string($description) && '' !== trim($description)) {
            $xw->writeElement('Description', trim($sequence->getDescription()));
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
            CurriculumInventorySequenceBlock::compareSequenceBlocksWithDefaultStrategy(...)
        );
        foreach ($topLevelSequenceBlocks as $block) {
            $this->writeSequenceBlockNode(
                $xw,
                $block,
                $inventory['sequence_block_references']['events'],
                $inventory['sequence_block_references']['competency_objects'],
                $institutionDomain
            );
        }
        $xw->endElement(); // </Sequence>

        //
        // Integration - currently not supported
        //


        $xw->endElement(); // </CurriculumInventory>
        $xw->endDocument();

        return $xw->outputMemory();
    }

    protected function writeCompetencyFrameworkNode(
        XmlWriter $xw,
        CurriculumInventoryReportInterface $report,
        string $reportId,
        string $institutionDomain,
        array $expectations
    ): void {
        // competency framework
        $xw->startElement('CompetencyFramework');

        // lom
        $xw->startElement('lom:lom');
        $xw->startElement('lom:general');
        $xw->startElement('lom:identifier');
        $xw->writeElement('lom:catalog', 'URI');
        $frameworkUri = "http://{$institutionDomain}/competency_framework/{$reportId}";
        $xw->writeElement('lom:entry', $frameworkUri);
        $xw->endElement(); // <lom:identifier>
        $xw->startElement('lom:title');
        $xw->writeElement('lom:string', 'Competency Framework for ' . $report->getName());
        $xw->endElement(); // <lom:title>
        $xw->endElement(); // <lom:general>
        $xw->endElement(); // <lom:lom>

        // includes
        $competencyIds = $expectations['framework']['includes']['pcrs_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createPcrsUri($id);
            $this->writeCompetencyFrameworkIncludesNode($xw, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['program_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'program_objective', $institutionDomain);
            $this->writeCompetencyFrameworkIncludesNode($xw, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['course_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'course_objective', $institutionDomain);
            $this->writeCompetencyFrameworkIncludesNode($xw, $uri);
        }
        $competencyIds = $expectations['framework']['includes']['session_objective_ids'];
        for ($i = 0, $n = count($competencyIds); $i < $n; $i++) {
            $id = $competencyIds[$i];
            $uri = $this->createCompetencyObjectUri($id, 'session_objective', $institutionDomain);
            $this->writeCompetencyFrameworkIncludesNode($xw, $uri);
        }
        // relations
        $relations = $expectations['framework']['relations']['program_objectives_to_pcrs'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri(
                $relation['rel1'],
                'program_objective',
                $institutionDomain
            );
            $relUri2 = $this->createPcrsUri($relation['rel2']);
            $relationshipUri = $this->createRelationshipUri('related');
            $this->writeCompetencyFrameworkRelationNode($xw, $relUri2, $relUri1, $relationshipUri);
        }
        $relations = $expectations['framework']['relations']['course_objectives_to_program_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri(
                $relation['rel1'],
                'program_objective',
                $institutionDomain
            );
            $relUri2 = $this->createCompetencyObjectUri(
                $relation['rel2'],
                'course_objective',
                $institutionDomain
            );
            $relationshipUri = $this->createRelationshipUri('narrower');
            $this->writeCompetencyFrameworkRelationNode($xw, $relUri1, $relUri2, $relationshipUri);
        }
        $relations = $expectations['framework']['relations']['session_objectives_to_course_objectives'];
        for ($i = 0, $n = count($relations); $i < $n; $i++) {
            $relation = $relations[$i];
            $relUri1 = $this->createCompetencyObjectUri(
                $relation['rel1'],
                'course_objective',
                $institutionDomain
            );
            $relUri2 = $this->createCompetencyObjectUri(
                $relation['rel2'],
                'session_objective',
                $institutionDomain
            );
            $relationshipUri = $this->createRelationshipUri('narrower');
            $this->writeCompetencyFrameworkRelationNode($xw, $relUri1, $relUri2, $relationshipUri);
        }
        $xw->endElement(); // </CompetencyFramework>
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $block the current sequence block
     * @param array $eventReferences A reference map of sequence blocks to events.
     * @param array $competencyObjectReferences A reference map of sequence blocks to competency objects.
     */
    protected function writeSequenceBlockNode(
        XmlWriter $xw,
        CurriculumInventorySequenceBlockInterface $block,
        array $eventReferences,
        array $competencyObjectReferences,
        string $institutionDomain
    ): void {
        $xw->startElement('SequenceBlock');
        $xw->writeAttribute('id', (string) $block->getId());
        switch ($block->getRequired()) {
            case CurriculumInventorySequenceBlockInterface::OPTIONAL:
                $xw->writeAttribute('required', 'Optional');
                break;
            case CurriculumInventorySequenceBlockInterface::REQUIRED:
                $xw->writeAttribute('required', 'Required');
                break;
            case CurriculumInventorySequenceBlockInterface::REQUIRED_IN_TRACK:
                $xw->writeAttribute('required', 'Required In Track');
                break;
        }
        switch ($block->getChildSequenceOrder()) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                $xw->writeAttribute('order', 'Ordered');
                break;
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
                $xw->writeAttribute('order', 'Unordered');
                break;
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
                $xw->writeAttribute('order', 'Parallel');
                break;
        }

        $min = $block->getMinimum();
        if ($min) {
            $xw->writeAttribute('minimum', (string) $min);
        }

        $max = $block->getMaximum();
        if ($max) {
            $xw->writeAttribute('maximum', (string) $max);
        }

        if ($block->hasTrack()) {
            $xw->writeAttribute('track', 'true');
        } else {
            $xw->writeAttribute('track', 'false');
        }

        $xw->writeElement('Title', $block->getTitle());

        $description = $block->getDescription();
        if (is_string($description) && '' !== trim($description)) {
            $xw->writeElement('Description', trim($block->getDescription()));
        }

        // add duration and/or start+end date
        $xw->startElement('Timing');
        $xw->writeElement('Duration', 'P' . $block->getDuration() . 'D'); // duration in days.
        if ($block->getStartDate()) {
            $xw->startElement('Dates');
            $xw->writeElement('StartDate', $block->getStartDate()->format('Y-m-d'));
            $xw->writeElement('EndDate', $block->getEndDate()->format('Y-m-d'));
            $xw->endElement(); // </Dates>
        }
        $xw->endElement(); // </Timing>

        // academicLevels
        $xw->startElement('SequenceBlockLevels');
        $xw->writeElement(
            'StartingAcademicLevel',
            "/CurriculumInventory/AcademicLevels/Level[@number='{$block->getStartingAcademicLevel()->getLevel()}']"
        );
        $xw->writeElement(
            'EndingAcademicLevel',
            "/CurriculumInventory/AcademicLevels/Level[@number='{$block->getEndingAcademicLevel()->getLevel()}']"
        );
        $xw->endElement(); // </SequenceBlockLevels>


        // clerkship type
        // map course clerkship type to "Clerkship Model"
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
            $xw->writeElement('ClerkshipModel', $clerkshipModel);
        }

        // link to competency objects
        if (array_key_exists($block->getId(), $competencyObjectReferences)) {
            $refs  = $competencyObjectReferences[$block->getId()];
            foreach ($refs['program_objectives'] as $id) {
                $uri = $this->createCompetencyObjectUri($id, 'program_objective', $institutionDomain);
                $this->writeCompetencyObjectReferenceNode($xw, $uri);
            }
            foreach ($refs['course_objectives'] as $id) {
                $uri = $this->createCompetencyObjectUri($id, 'course_objective', $institutionDomain);
                $this->writeCompetencyObjectReferenceNode($xw, $uri);
            }
        }
        // pre-conditions and post-conditions are n/a

        // link to events
        if (array_key_exists($block->getId(), $eventReferences)) {
            $refs = $eventReferences[$block->getId()];
            foreach ($refs as $reference) {
                $xw->startElement('SequenceBlockEvent');
                if ($reference['optional']) {
                    $xw->writeAttribute('required', 'false');
                } else {
                    $xw->writeAttribute('required', 'true');
                }
                $refUri = "/CurriculumInventory/Events/Event[@id='E{$reference['event_id']}']";
                $xw->writeElement('EventReference', $refUri);

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
                $xw->endElement(); // </SequenceBlockEvent>
            }
        }

        $children = $block->getChildrenAsSortedList();
        if (! empty($children)) {
            $order = 0;
            $isOrdered = CurriculumInventorySequenceBlockInterface::ORDERED === $block->getChildSequenceOrder();
            foreach ($children as $child) {
                // apply an incremental sort order for "ordered" sequence blocks
                if ($isOrdered) {
                    $order++;
                }
                $ref = "/CurriculumInventory/Sequence/SequenceBlock[@id='{$child->getId()}']";
                $xw->startElement('SequenceBlockReference');
                if ($order) {
                    $xw->writeAttribute('order', (string) $order);
                }
                $xw->text($ref);
                $xw->endElement(); // </SequenceBlockReference>
            }
        }
        $xw->endElement(); // </SequenceBlock>

        // recursively generate XML for nested sequence blocks
        if (! empty($children)) {
            foreach ($children as $child) {
                $this->writeSequenceBlockNode(
                    $xw,
                    $child,
                    $eventReferences,
                    $competencyObjectReferences,
                    $institutionDomain,
                );
            }
        }
    }

    /**
     * @param string $title The competency object's title.
     * @param string $uri An URI that uniquely identifies the competency object.
     */
    protected function writeCompetencyObjectNode(XmlWriter $xw, string $title, string $uri, string $category): void
    {
        $xw->startElement('CompetencyObject');
        $xw->startElement('lom:lom');
        $xw->startElement('lom:general');
        $xw->startElement('lom:identifier');
        $xw->writeElement('lom:catalog', 'URI');
        $xw->writeElement('lom:entry', $uri);
        $xw->endElement(); // </lom:identifier>
        $xw->startElement('lom:title');
        $xw->writeElement('lom:string', trim(strip_tags($title)));
        $xw->endElement(); // </lom:title>
        $xw->endElement(); // </lom:general>
        $xw->endElement(); // </lom:lom>
        $xw->startElement('co:Category');
        $xw->writeAttribute('term', $category);
        $xw->endElement(); // </co:Category>
        $xw->endElement(); // </CompetencyElement>
    }

    /**
     * @param string $uri An URI that uniquely identifies the competency object.
     */
    protected function writeCompetencyObjectReferenceNode(XmlWriter $xw, string $uri): void
    {
        $ref =
            "/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/lom:entry='{$uri}']";
        $xw->writeElement('CompetencyObjectReference', $ref);
    }

    protected function writeCompetencyFrameworkIncludesNode(XmlWriter $xw, string $uri): void
    {
        $xw->startElement('cf:Includes');
        $xw->writeElement('cf:Catalog', 'URI');
        $xw->writeElement('cf:Entry', $uri);
        $xw->endElement(); // </cf:Includes>
    }

    protected function writeCompetencyFrameworkRelationNode(
        XmlWriter $xw,
        string $relUri1,
        string $relUri2,
        string $relationshipUri
    ): void {
        $xw->startElement('cf:Relation');
        $xw->startElement('cf:Reference1');
        $xw->writeElement('cf:Catalog', 'URI');
        $xw->writeElement('cf:Entry', $relUri1);
        $xw->endElement(); // </cf:Reference1>
        $xw->writeElement('cf:Relationship', $relationshipUri);
        $xw->startElement('cf:Reference2');
        $xw->writeElement('cf:Catalog', 'URI');
        $xw->writeElement('cf:Entry', $relUri2);
        $xw->endElement(); // </cf:Reference2>
        $xw->endElement(); // </cf:Relation>
    }

    protected function createRelationshipUri(string $type): string
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
     */
    protected function createCompetencyObjectUri(int $id, string $type, string $institutionDomain): string
    {
        return "http://{$institutionDomain}/{$type}/{$id}";
    }

    /**
     * Returns a URI that identifies a given PCRS as defined by the AAMC.
     * @param string $pcrsPartialUri A part of the URI that uniquely identifies te PCRS competency.
     */
    protected function createPcrsUri(string $pcrsPartialUri): string
    {
        return "https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#{$pcrsPartialUri}";
    }
}
