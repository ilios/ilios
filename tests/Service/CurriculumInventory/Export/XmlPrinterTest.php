<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory\Export;

use App\Entity\CurriculumInventoryInstitution;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventorySequence;
use App\Entity\Program;
use App\Service\CurriculumInventory\Export\XmlPrinter;
use App\Tests\TestCase;
use DateTime;

/**
 * Class AggregatorTest
 * @package App\Tests\Service\CurriculumInventory\Export
 */
class XmlPrinterTest extends TestCase
{
    protected XmlPrinter $printer;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->printer = new XmlPrinter();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function inventoryDataProvider(): array
    {
        $program = new Program();
        $program->setTitle('Doctor of Rocket Surgery (DRS)');
        $program->setId(100);
        $sequence = new CurriculumInventorySequence();
        $sequence->setDescription('Lorem Ipsum');
        $report = new CurriculumInventoryReport();
        $report->setSequence($sequence);
        $report->setProgram($program);
        $report->setYear(2020);
        $report->setDescription('This is BEANS.');
        $report->setStartDate(new DateTime('2019-07-01'));
        $report->setEndDate(new DateTime('2020-06-30'));
        $report->setName('DRS Curriculum Map 2019-2020');
        $institution = new CurriculumInventoryInstitution();
        $institution->setName('School of Rocket Surgery');
        $institution->setAamcCode('1138');
        $institution->setAddressCity('Anytown');
        $institution->setAddressStateOrProvince('Anyplace');
        $institution->setAddressStreet('Main Street 123');
        $institution->setAddressCountryCode('US');
        $institution->setAddressZipCode('12345');
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
                    'assessment_option_name' => null,
                    'duration' => 120.0,
                    'keywords' => [
                        [
                            'event_id' => 1000,
                            'id' => 'D000758',
                            'source' => 'MeSH',
                            'name' => 'Anesthesia'
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
            ],
            'expectations' => [
                'program_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Program Objective 1',
                    ],
                ],
                'course_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Course Objective 1',
                    ],
                ],
                'session_objectives' => [
                    [
                        'id' => 1,
                        'title' => 'Session Objective 1',
                    ],
                ],
                'framework' => [
                    'includes' => [
                        'pcrs_ids' => [],
                        'program_objective_ids' => [ 1 ],
                        'course_objective_ids' => [ 1 ],
                        'session_objective_ids' => [ 1 ],
                    ],
                    'relations' => [
                        'program_objectives_to_pcrs' => [],
                        'course_objectives_to_program_objectives' => [],
                        'session_objectives_to_course_objectives' => [],
                    ],
                ]
            ],
            'sequence_block_references' => []
        ];

        return [ [ $inventory ] ];
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\XmlPrinter::print
     * @dataProvider inventoryDataProvider
     * @param array $inventory
     */
    public function testPrintReport(array $inventory): void
    {
        $dom = $this->printer->print($inventory);
        $xml = simplexml_import_dom($dom);

        // <ReportId>
        $reportId = $xml->ReportID;
        $this->assertEquals('2020x100xx1595006118', (string)$reportId);
        $this->assertEquals('idd:example.university.edu:cireport', (string)$reportId->attributes()['domain']);

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
        $this->assertEquals(
            'idd:example.university.edu:program',
            (string)$xml->Program->ProgramID->attributes()['domain']
        );
        // <Title>
        $this->assertEquals('DRS Curriculum Map 2019-2020', (string)$xml->Title);

        // <ReportDate>, <ReportingStartDate>, <ReportingEndDate>
        $this->assertEquals('2020-07-17', (string)$xml->ReportDate);
        $this->assertEquals('2019-07-01', (string)$xml->ReportingStartDate);
        $this->assertEquals('2020-06-30', (string)$xml->ReportingEndDate);

        // <Description>
        $this->assertEquals('This is BEANS.', (string)$xml->Description);

        // <Events>
        $this->assertCount(1, $xml->Events->Event);
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

        // <Expectations>
        $this->assertCount(3, $xml->Expectations->CompetencyObject);

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
            (string) $xml->Expectations->CompetencyObject[0]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/course_objective/1',
            (string) $xml->Expectations->CompetencyObject[1]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Course Objective 1',
            (string) $xml->Expectations->CompetencyObject[1]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'sequence-block-level-competency',
            (string) $xml->Expectations->CompetencyObject[1]->children('co', true)->Category->attributes()['term']
        );
        $this->assertEquals(
            'http://example.university.edu/session_objective/1',
            (string) $xml->Expectations->CompetencyObject[2]->children('lom', true)->lom->general->identifier->entry
        );
        $this->assertEquals(
            'Session Objective 1',
            (string) $xml->Expectations->CompetencyObject[2]->children('lom', true)->lom->general->title->string
        );
        $this->assertEquals(
            'event-level-competency',
            (string) $xml->Expectations->CompetencyObject[2]->children('co', true)->Category->attributes()['term']
        );
    }
}
