<?php

namespace App\Tests\Service\CurriculumInventory\Export;

use App\Entity\CurriculumInventoryReport;
use App\Entity\Manager\CurriculumInventoryInstitutionManager;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Entity\Program;
use App\Service\Config;
use App\Service\CurriculumInventory\Export\Aggregator;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

/**
 * Class AggregatorTest
 * @package App\Tests\Service\CurriculumInventory\Export
 */
class AggregatorTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */
    protected $reportManager;

    /**
     * @var m\MockInterface
     */
    protected $institutionManager;

    /**
     * @var m\MockInterface
     */
    protected $config;

    /**
     * @var Aggregator
     */
    protected $aggregator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->reportManager = m::mock(CurriculumInventoryReportManager::class);
        $this->institutionManager = m::mock(CurriculumInventoryInstitutionManager::class);
        $this->config = m::mock(Config::class);
        $this->aggregator = new Aggregator($this->reportManager, $this->institutionManager, $this->config);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->aggregator);
        unset($this->reportManager);
        unset($this->institutionManager);
        unset($this->config);
        parent::tearDown();
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::addKeywordsToEvents
     */
    public function testAddKeywordsToEvents()
    {
        $event1 = [];
        $event2 = [];
        $keyword1 = ['event_id' => 10];
        $keyword2 = ['event_id' => 10];
        $keyword3 = ['event_id' => 30];

        $events = [10 => $event1, 20 => $event2];
        $keywords = [$keyword1, $keyword2, $keyword3];

        $events = Aggregator::addKeywordsToEvents($events, $keywords);

        $this->assertEquals(2, count($events));
        $this->assertEquals(2, count($events[10]['keywords']));
        $this->assertEquals($keyword1, $events[10]['keywords'][0]);
        $this->assertEquals($keyword2, $events[10]['keywords'][1]);
        $this->assertFalse(array_key_exists('keywords', $events[20]));
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::addResourceTypesToEvents
     */
    public function testAddResourceTypesToEvents()
    {
        $resourceType1 = ['event_id' => 10];
        $resourceType2 = ['event_id' => 10];
        $resourceType3 = ['event_id' => 30];

        $events = [10 => [], 20 => []];
        $keywords = [$resourceType1, $resourceType2, $resourceType3];

        $events = Aggregator::addResourceTypesToEvents($events, $keywords);

        $this->assertEquals(2, count($events));
        $this->assertEquals(2, count($events[10]['resource_types']));
        $this->assertEquals($resourceType1, $events[10]['resource_types'][0]);
        $this->assertEquals($resourceType2, $events[10]['resource_types'][1]);
        $this->assertFalse(array_key_exists('resource_types', $events[20]));
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::addCompetencyObjectReferencesToEvents
     */
    public function testAddCompetencyObjectReferencesToEvents()
    {
        $events = [
            10 => [],
            20 => [],
            30 => [],
        ];

        $ref1 = 'Does';
        $ref2 = 'Not';
        $ref3 = 'Matter';
        $references = [
            10 => $ref1,
            20 => $ref2,
            50 => $ref3,
        ];
        $events = Aggregator::addCompetencyObjectReferencesToEvents($events, $references);

        $this->assertEquals($ref1, $events[10]['competency_object_references']);
        $this->assertEquals($ref2, $events[20]['competency_object_references']);
        $this->assertTrue(array_key_exists(30, $events));
        $this->assertFalse(array_key_exists('competency_object_references', $events[30]));
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::getConsolidatedObjectivesMap
     */
    public function testGetConsolidatedObjectivesMap()
    {
        $objectives = [
            ['id' => 1, 'ancestor_id' => null],
            ['id' => 3, 'ancestor_id' => null],
            ['id' => 10, 'ancestor_id' => 1],
            ['id' => 20, 'ancestor_id' => null],
            ['id' => 40, 'ancestor_id' => 2],
            ['id' => 30, 'ancestor_id' => 2],
            ['id' => 50, 'ancestor_id' => 3],
            ['id' => 60, 'ancestor_id' => 3],
            ['id' => 70, 'ancestor_id' => 3],
        ];

        $map = Aggregator::getConsolidatedObjectivesMap($objectives);

        $this->assertEquals(5, count($map));
        $this->assertEquals(40, $map[30]);
        $this->assertEquals(70, $map[50]);
        $this->assertEquals(70, $map[60]);
        $this->assertEquals(70, $map[3]);
        $this->assertEquals(10, $map[1]);
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::getData
     */
    public function testGetFailsIfReportHasNoProgram()
    {
        $this->expectExceptionMessage('No program found for report with id = 1.');
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getProgram')
            ->andReturn(null);
        $report->shouldReceive('getId')
            ->andReturn(1);
        $this->aggregator->getData($report);
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::getData
     */
    public function testGetFailsIfProgramHasNoSchool()
    {
        $this->expectExceptionMessage('No school found for program with id = 1.');
        $program = m::mock(Program::class);
        $program->shouldReceive('getSchool')
            ->andReturn(null);
        $program->shouldReceive('getId')
            ->andReturn(1);
        $report = m::mock(CurriculumInventoryReport::class);
        $report->shouldReceive('getProgram')
            ->andReturn($program);
        $this->aggregator->getData($report);
    }

    /**
     * @covers \App\Service\CurriculumInventory\Export\Aggregator::getData
     * @todo Implement this monster of a test. [ST 2018/07/18]
     */
    public function testGetData()
    {
        $this->markTestIncomplete('to be implemented.');
    }
}
