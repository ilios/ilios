<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory\Export;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\Course;
use App\Entity\CourseClerkshipType;
use App\Entity\CourseClerkshipTypeInterface;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryInstitution;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventorySequence;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\Program;
use App\Service\CurriculumInventory\Export\XmlPrinter;
use App\Tests\TestCase;
use DateTime;

/**
 * Class AggregatorTest
 * @package App\Tests\Service\CurriculumInventory\Export
 */
#[CoversClass(XmlPrinter::class)]
class XmlPrinterTest extends TestCase
{
    protected XmlPrinter $printer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->printer = new XmlPrinter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->printer);
    }

    public static function inventoryDataProvider(): array
    {
        $academicLevel1 = new CurriculumInventoryAcademicLevel();
        $academicLevel1->setName('Year 1');
        $academicLevel1->setDescription('Academic Level Year 1');
        $academicLevel1->setLevel(1);
        $academicLevel2 = new CurriculumInventoryAcademicLevel();
        $academicLevel2->setName('Year 2');
        $academicLevel2->setDescription('Academic Level Year 2');
        $academicLevel2->setLevel(2);
        $academicLevel3 = new CurriculumInventoryAcademicLevel();
        $academicLevel3->setName('Year 3');
        $academicLevel3->setDescription('Academic Level Year 3');
        $academicLevel3->setLevel(3);
        $program = new Program();
        $program->setTitle('Doctor of Rocket Surgery (DRS)');
        $program->setId(100);
        $sequence = new CurriculumInventorySequence();
        $sequence->setDescription('Lorem Ipsum');
        $report = new CurriculumInventoryReport();
        $report->setId(999);
        $report->setSequence($sequence);
        $report->setProgram($program);
        $report->setYear(2020);
        $report->setDescription('This is BEANS.');
        $report->setStartDate(new DateTime('2019-07-01'));
        $report->setEndDate(new DateTime('2020-06-30'));
        $report->setName('DRS Curriculum Map 2019-2020');
        $report->addAcademicLevel($academicLevel1);
        $report->addAcademicLevel($academicLevel2);
        $report->addAcademicLevel($academicLevel3);
        $institution = new CurriculumInventoryInstitution();
        $institution->setName('School of Rocket Surgery');
        $institution->setAamcCode('1138');
        $institution->setAddressCity('Anytown');
        $institution->setAddressStateOrProvince('Anyplace');
        $institution->setAddressStreet('Main Street 123');
        $institution->setAddressCountryCode('US');
        $institution->setAddressZipCode('12345');
        $clerkshipType = new CourseClerkshipType();
        $clerkshipType->setId(CourseClerkshipTypeInterface::LONGITUDINAL);
        $course = new Course();
        $course->setClerkshipType($clerkshipType);
        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setTitle('Top Level Sequence Block 1');
        $sequenceBlock1->setDescription('This is the top level sequence block 1.');
        $sequenceBlock1->setDuration(10);
        $sequenceBlock1->setStartDate(new DateTime('2019-10-01'));
        $sequenceBlock1->setEndDate(new DateTime('2019-10-10'));
        $sequenceBlock1->setStartingAcademicLevel($academicLevel1);
        $sequenceBlock1->setEndingAcademicLevel($academicLevel2);
        $sequenceBlock1->setRequired(CurriculumInventorySequenceBlockInterface::OPTIONAL);
        $sequenceBlock1->setChildSequenceOrder(CurriculumInventorySequenceBlockInterface::UNORDERED);
        $sequenceBlock1->setMinimum(2);
        $sequenceBlock1->setMaximum(5);
        $sequenceBlock1->setTrack(false);
        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setParent($sequenceBlock1);
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setTitle('Nested Sequence Block 2');
        $sequenceBlock2->setDescription('This is the nested sequence block 2.');
        $sequenceBlock2->setDuration(4);
        $sequenceBlock2->setStartDate(new DateTime('2019-10-02'));
        $sequenceBlock2->setEndDate(new DateTime('2019-10-05'));
        $sequenceBlock2->setStartingAcademicLevel($academicLevel1);
        $sequenceBlock2->setEndingAcademicLevel($academicLevel2);
        $sequenceBlock2->setRequired(CurriculumInventorySequenceBlockInterface::REQUIRED);
        $sequenceBlock2->setChildSequenceOrder(CurriculumInventorySequenceBlockInterface::ORDERED);
        $sequenceBlock2->setMinimum(1);
        $sequenceBlock2->setMaximum(1);
        $sequenceBlock2->setTrack(true);
        $sequenceBlock2->setCourse($course);
        $sequenceBlock1->addChild($sequenceBlock2);
        $academicLevel1->addStartingSequenceBlock($sequenceBlock1);
        $academicLevel1->addStartingSequenceBlock($sequenceBlock2);
        $academicLevel2->addEndingSequenceBlock($sequenceBlock1);
        $academicLevel2->addEndingSequenceBlock($sequenceBlock2);
        $report->addSequenceBlock($sequenceBlock1);
        $createdAt = new DateTime('2020-07-17T17:15:18+00:00');
        $inventory = [
            'report' => $report,
            'created_at' => $createdAt->getTimestamp(),
            'supporting_link' => 'https://example.university.edu/supportinglink',
            'institution_domain' => 'example.university.edu',
            'institution' => $institution,
            'events' => [
                [
                    'event_id' => 1000,
                    'title' => 'rocket surgery 101',
                    'description' => 'students will work as a team to acquire basic skills in rocket surgery',
                    'method_id' => 'XY001',
                    'is_assessment_method' => false,
                    'duration' => 120.0,
                    'keywords' => [
                        [
                            'event_id' => 1000,
                            'id' => 'D000758',
                            'source' => 'MeSH',
                            'name' => 'Anesthesia',
                        ],
                        [
                            'event_id' => 1000,
                            'id' => 1,
                            'source' => 'Topics',
                            'name' => 'Anesthesiology',
                        ],
                    ],
                    'competency_object_references' => [
                        'session_objectives' => [ 1 ],
                        'course_objectives' => [ 1 ],
                        'program_objectives' => [ 1 ],
                    ],
                ],
                [
                    'event_id' => 2000,
                    'title' => 'something else',
                    'description' => 'sure thing',
                    'method_id' => 'ZZZ0002',
                    'is_assessment_method' => true,
                    'assessment_option_name' => 'formative',
                    'duration' => 60.0,
                    'keywords' => [
                        [
                            'event_id' => 2000,
                            'id' => 'D000005',
                            'source' => 'MeSH',
                            'name' => 'Abdomen',
                        ],
                        [
                            'event_id' => 2000,
                            'id' => 2,
                            'source' => 'Difficulties',
                            'name' => 'Beginner',
                        ],
                    ],
                    'competency_object_references' => [
                        'session_objectives' => [ 2 ],
                        'course_objectives' => [ 2 ],
                        'program_objectives' => [ 2 ],
                    ],
                ],
            ],
            'expectations' => [
                'program_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Program Objective 1',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Program Objective 2',
                    ],
                ],
                'course_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Course Objective 1',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Course Objective 2',
                    ],
                ],
                'session_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Session Objective 1',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Session Objective 2',
                    ],
                ],
                'framework' => [
                    'includes' => [
                        'pcrs_ids' => [
                            'aamc-pcrs-comp-c0119', 'aamc-pcrs-comp-c0219',

                        ],
                        'program_objective_ids' => [ 1, 2 ],
                        'course_objective_ids' => [ 1, 2 ],
                        'session_objective_ids' => [ 1, 2 ],
                    ],
                    'relations' => [
                        'program_objectives_to_pcrs' => [
                            [ 'rel1' => 1, 'rel2' => 'aamc-pcrs-comp-c0119', ],
                            [ 'rel1' => 2, 'rel2' => 'aamc-pcrs-comp-c0219', ],
                        ],
                        'course_objectives_to_program_objectives' => [
                            [ 'rel1' => 1, 'rel2' => 1, ],
                            [ 'rel1' => 2, 'rel2' => 2, ],
                        ],
                        'session_objectives_to_course_objectives' => [
                            [ 'rel1' => 1, 'rel2' => 1, ],
                            [ 'rel1' => 2, 'rel2' => 2, ],
                        ],
                    ],
                ],
            ],
            'sequence_block_references' => [
                'events' => [
                    2 => [
                        [ 'id' => 2, 'event_id' => 100, 'optional' => false, ],
                        [ 'id' => 2, 'event_id' => 200, 'optional' => true, ],
                    ],
                ],
                'competency_objects' => [
                    2 => [
                        'course_objectives' => [ 1, 2 ],
                        'program_objectives' => [ 1, 2 ],
                    ],
                ],
            ],
        ];

        return [ [ $inventory ] ];
    }

    #[DataProvider('inventoryDataProvider')]
    public function testPrintReport(array $inventory): void
    {
        $xml = simplexml_load_string($this->printer->print($inventory));

        // <ReportId>
        $reportId = $xml->ReportID;
        $this->assertEquals('2020x100x999x1595006118', (string)$reportId);
        $this->assertEquals('idd:example.university.edu:cireport', $reportId->attributes()['domain']);

        // <Institution>
        $institution = $xml->Institution;
        $this->assertEquals(
            'School of Rocket Surgery',
            (string)$institution->children('m', true)->InstitutionName
        );
        $this->assertEquals('1138', (string)$institution->children('m', true)->InstitutionID);
        $address = $institution->children('m', true)->Address;
        $this->assertEquals(
            'Main Street 123',
            (string)$address->children('a', true)->StreetAddressLine
        );
        $this->assertEquals('Anytown', (string)$address->children('a', true)->City);
        $this->assertEquals('Anyplace', (string)$address->children('a', true)->StateOrProvince);
        $this->assertEquals('12345', (string)$address->children('a', true)->PostalCode);
        $this->assertEquals('US', (string)$address->children('a', true)->Country->CountryCode);

        // <Program>
        $this->assertEquals('Doctor of Rocket Surgery (DRS)', (string)$xml->Program->ProgramName);
        $this->assertEquals('100', (string)$xml->Program->ProgramID);
        $this->assertEquals('idd:example.university.edu:program', $xml->Program->ProgramID->attributes()['domain']);
        // <Title>
        $this->assertEquals('DRS Curriculum Map 2019-2020', (string)$xml->Title);

        // <ReportDate>, <ReportingStartDate>, <ReportingEndDate>
        $this->assertEquals(date('Y-m-d'), (string)$xml->ReportDate);
        $this->assertEquals('2019-07-01', (string)$xml->ReportingStartDate);
        $this->assertEquals('2020-06-30', (string)$xml->ReportingEndDate);

        // <Description>
        $this->assertEquals('This is BEANS.', (string)$xml->Description);

        // <SupportingLink>
        $this->assertEquals('https://example.university.edu/supportinglink', (string)$xml->SupportingLink);

        // <Events>
        $this->assertCount(2, $xml->Events->Event);
        $event = $xml->Events->children()[0];
        $this->assertEquals('E1000', $event->attributes()['id']);
        $this->assertEquals('rocket surgery 101', (string)$event->Title);
        $this->assertEquals(
            'students will work as a team to acquire basic skills in rocket surgery',
            (string)$event->Description
        );
        $this->assertCount(2, $event->Keyword);
        $this->assertEquals('MeSH', $event->Keyword[0]->attributes('hx', true)['source']);
        $this->assertEquals('D000758', $event->Keyword[0]->attributes('hx', true)['id']);
        $this->assertEquals('Anesthesia', (string) $event->Keyword[0]->children('hx', true)[0]);
        $this->assertEquals('Topics', $event->Keyword[1]->attributes('hx', true)['source']);
        $this->assertEquals('1', $event->Keyword[1]->attributes('hx', true)['id']);
        $this->assertEquals('Anesthesiology', (string) $event->Keyword[1]->children('hx', true)[0]);

        $this->assertCount(3, $event->CompetencyObjectReference);
        $this->assertStringContainsString(
            'http://example.university.edu/program_objective/1',
            (string) $event->CompetencyObjectReference[0]
        );
        $this->assertStringContainsString(
            'http://example.university.edu/course_objective/1',
            (string) $event->CompetencyObjectReference[1]
        );
        $this->assertStringContainsString(
            'http://example.university.edu/session_objective/1',
            (string) $event->CompetencyObjectReference[2]
        );
        $this->assertEquals('XY001', (string) $event->InstructionalMethod);
        $this->assertEquals('true', $event->InstructionalMethod->attributes()['primary']);
        $this->assertEquals('PT120M', (string) $event->EventDuration);
        $this->assertEquals(
            (string) $event->EventDuration,
            $event->InstructionalMethod->attributes()['instructionalMethodDuration']
        );
        $event = $xml->Events->children()[1];
        $this->assertEquals('E2000', $event->attributes()['id']);
        $this->assertEquals('something else', (string)$event->Title);
        $this->assertEquals('sure thing', (string)$event->Description);
        $this->assertCount(2, $event->Keyword);
        $this->assertEquals('MeSH', $event->Keyword[0]->attributes('hx', true)['source']);
        $this->assertEquals('D000005', $event->Keyword[0]->attributes('hx', true)['id']);
        $this->assertEquals('Abdomen', (string) $event->Keyword[0]->children('hx', true)[0]);
        $this->assertEquals('Difficulties', $event->Keyword[1]->attributes('hx', true)['source']);
        $this->assertEquals('2', $event->Keyword[1]->attributes('hx', true)['id']);
        $this->assertEquals('Beginner', (string) $event->Keyword[1]->children('hx', true)[0]);

        $this->assertCount(3, $event->CompetencyObjectReference);
        $this->assertStringContainsString(
            'http://example.university.edu/program_objective/2',
            (string) $event->CompetencyObjectReference[0]
        );
        $this->assertStringContainsString(
            'http://example.university.edu/course_objective/2',
            (string) $event->CompetencyObjectReference[1]
        );
        $this->assertStringContainsString(
            'http://example.university.edu/session_objective/2',
            (string) $event->CompetencyObjectReference[2]
        );
        $this->assertEquals('ZZZ0002', (string) $event->AssessmentMethod);

        // <Expectations>
        $this->assertCount(6, $xml->Expectations->CompetencyObject);

        $this->assertEquals(
            'http://example.university.edu/program_objective/1',
            (string) $xml->Expectations->CompetencyObject[0]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Program Objective 1',
            (string) $xml->Expectations->CompetencyObject[0]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'program-level-competency',
            $xml->Expectations->CompetencyObject[0]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/2',
            (string) $xml->Expectations->CompetencyObject[1]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Program Objective 2',
            (string) $xml->Expectations->CompetencyObject[1]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'program-level-competency',
            $xml->Expectations->CompetencyObject[1]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/1',
            (string) $xml->Expectations->CompetencyObject[2]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Course Objective 1',
            (string) $xml->Expectations->CompetencyObject[2]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'sequence-block-level-competency',
            $xml->Expectations->CompetencyObject[2]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/2',
            (string) $xml->Expectations->CompetencyObject[3]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Course Objective 2',
            (string) $xml->Expectations->CompetencyObject[3]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'sequence-block-level-competency',
            $xml->Expectations->CompetencyObject[3]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/1',
            (string) $xml->Expectations->CompetencyObject[4]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Session Objective 1',
            (string) $xml->Expectations->CompetencyObject[4]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'event-level-competency',
            $xml->Expectations->CompetencyObject[4]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/2',
            (string) $xml->Expectations->CompetencyObject[5]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Session Objective 2',
            (string) $xml->Expectations->CompetencyObject[5]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'event-level-competency',
            $xml->Expectations->CompetencyObject[5]->children('co', true)->Category->attributes()['term']
        );
        // <CompetencyFramework>
        $competencyFramework = $xml->Expectations->CompetencyFramework;
        $this->assertEquals(
            'http://example.university.edu/competency_framework/2020x100x999x1595006118',
            (string)$competencyFramework->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Competency Framework for DRS Curriculum Map 2019-2020',
            (string)$competencyFramework->children('lom', true)->lom->general->title->string
        );
        // <Includes>
        $includes = $competencyFramework->children('cf', true)->Includes;
        $this->assertCount(8, $includes);
        $this->assertEquals(
            'https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#aamc-pcrs-comp-c0119',
            (string)$includes[0]->Entry
        );
        $this->assertEquals(
            'https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#aamc-pcrs-comp-c0219',
            (string)$includes[1]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/1',
            (string)$includes[2]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/2',
            (string)$includes[3]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/1',
            (string)$includes[4]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/2',
            (string)$includes[5]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/1',
            (string)$includes[6]->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/2',
            (string)$includes[7]->Entry
        );
        foreach ($competencyFramework->children('cf', true)->Includes as $include) {
            $this->assertEquals('URI', (string) $include->Catalog);
        }
        // <Relation>
        $relations = $competencyFramework->children('cf', true)->Relation;
        $this->assertCount(6, $relations);
        $this->assertEquals(
            'https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#aamc-pcrs-comp-c0119',
            (string)$relations[0]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/1',
            (string)$relations[0]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#related',
            (string)$relations[0]->Relationship
        );
        $this->assertEquals(
            'https://services.aamc.org/30/ci-school-web/pcrs/PCRS.html#aamc-pcrs-comp-c0219',
            (string)$relations[1]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/2',
            (string)$relations[1]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#related',
            (string)$relations[1]->Relationship
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/1',
            (string)$relations[2]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/1',
            (string)$relations[2]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#narrower',
            (string)$relations[2]->Relationship
        );
        $this->assertEquals(
            'http://example.university.edu/program_objective/2',
            (string)$relations[3]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/2',
            (string)$relations[3]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#narrower',
            (string)$relations[3]->Relationship
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/1',
            (string)$relations[4]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/1',
            (string)$relations[4]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#narrower',
            (string)$relations[4]->Relationship
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/2',
            (string)$relations[5]->Reference1->Entry
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/2',
            (string)$relations[5]->Reference2->Entry
        );
        $this->assertEquals(
            'http://www.w3.org/2004/02/skos/core#narrower',
            (string)$relations[5]->Relationship
        );
        // <AcademicLevels>
        $this->assertEquals('2', (string)$xml->AcademicLevels->LevelsInProgram);
        $this->assertCount(2, $xml->AcademicLevels->Level);
        $level = $xml->AcademicLevels->Level;
        $this->assertEquals('1', $level->attributes()['number']);
        $this->assertEquals('Year 1', (string)$level->Label);
        $this->assertEquals('Academic Level Year 1', (string)$level->Description);
        // <SequenceBlock>
        $this->assertCount(2, $xml->Sequence->SequenceBlock);
        $block = $xml->Sequence->SequenceBlock[0];
        $this->assertEquals('1', $block->attributes()['id']);
        $this->assertEquals('Optional', $block->attributes()['required']);
        $this->assertEquals('Unordered', $block->attributes()['order']);
        $this->assertEquals('2', $block->attributes()['minimum']);
        $this->assertEquals('5', $block->attributes()['maximum']);
        $this->assertEquals('false', $block->attributes()['track']);
        $this->assertEquals('Top Level Sequence Block 1', (string)$block->Title);
        $this->assertEquals('This is the top level sequence block 1.', (string)$block->Description);
        $this->assertEquals('P10D', (string)$block->Timing->Duration);
        $this->assertEquals('2019-10-01', (string)$block->Timing->Dates->StartDate);
        $this->assertEquals('2019-10-10', (string)$block->Timing->Dates->EndDate);
        $this->assertEquals(
            '/CurriculumInventory/AcademicLevels/Level[@number=\'1\']',
            (string)$block->SequenceBlockLevels->StartingAcademicLevel
        );
        $this->assertEquals(
            '/CurriculumInventory/AcademicLevels/Level[@number=\'2\']',
            (string)$block->SequenceBlockLevels->EndingAcademicLevel
        );
        $this->assertEquals(
            '/CurriculumInventory/Sequence/SequenceBlock[@id=\'2\']',
            (string)$block->SequenceBlockReference
        );
        $block = $xml->Sequence->SequenceBlock[1];
        $this->assertEquals('2', $block->attributes()['id']);
        $this->assertEquals('Required', $block->attributes()['required']);
        $this->assertEquals('Ordered', $block->attributes()['order']);
        $this->assertEquals('1', $block->attributes()['minimum']);
        $this->assertEquals('1', $block->attributes()['maximum']);
        $this->assertEquals('true', $block->attributes()['track']);
        $this->assertEquals('Nested Sequence Block 2', (string)$block->Title);
        $this->assertEquals('This is the nested sequence block 2.', (string)$block->Description);
        $this->assertEquals('P4D', (string)$block->Timing->Duration);
        $this->assertEquals('2019-10-02', (string)$block->Timing->Dates->StartDate);
        $this->assertEquals('2019-10-05', (string)$block->Timing->Dates->EndDate);
        $this->assertEquals(
            '/CurriculumInventory/AcademicLevels/Level[@number=\'1\']',
            (string)$block->SequenceBlockLevels->StartingAcademicLevel
        );
        $this->assertEquals(
            '/CurriculumInventory/AcademicLevels/Level[@number=\'2\']',
            (string)$block->SequenceBlockLevels->EndingAcademicLevel
        );
        $this->assertEquals('rotation', (string)$block->ClerkshipModel);
        $this->assertCount(4, $block->CompetencyObjectReference);
        $this->assertEquals(
            '/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/'
            . 'lom:entry=\'http://example.university.edu/program_objective/1\']',
            $block->CompetencyObjectReference[0]
        );
        $this->assertEquals(
            '/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/'
            . 'lom:entry=\'http://example.university.edu/program_objective/2\']',
            $block->CompetencyObjectReference[1]
        );
        $this->assertEquals(
            '/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/'
            . 'lom:entry=\'http://example.university.edu/course_objective/1\']',
            $block->CompetencyObjectReference[2]
        );
        $this->assertEquals(
            '/CurriculumInventory/Expectations/CompetencyObject[lom:lom/lom:general/lom:identifier/'
            . 'lom:entry=\'http://example.university.edu/course_objective/2\']',
            $block->CompetencyObjectReference[3]
        );

        $this->assertCount(2, $block->SequenceBlockEvent);
        $this->assertEquals('true', $block->SequenceBlockEvent[0]->attributes()['required']);
        $this->assertEquals(
            '/CurriculumInventory/Events/Event[@id=\'E100\']',
            (string)$block->SequenceBlockEvent[0]->EventReference
        );
        $this->assertEquals('false', $block->SequenceBlockEvent[1]->attributes()['required']);
        $this->assertEquals(
            '/CurriculumInventory/Events/Event[@id=\'E200\']',
            (string)$block->SequenceBlockEvent[1]->EventReference
        );
    }
}
