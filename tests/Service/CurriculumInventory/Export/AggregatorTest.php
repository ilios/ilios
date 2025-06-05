<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory\Export;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Repository\CurriculumInventoryInstitutionRepository;
use App\Service\Config;
use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\Manager;
use App\Tests\TestCase;
use Mockery as m;

/**
 * Class AggregatorTest
 * @package App\Tests\Service\CurriculumInventory\Export
 */
#[CoversClass(Aggregator::class)]
final class AggregatorTest extends TestCase
{
    protected m\MockInterface $manager;
    protected m\MockInterface $institutionRepository;
    protected m\MockInterface $config;
    protected Aggregator $aggregator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = m::mock(Manager::class);
        $this->institutionRepository = m::mock(CurriculumInventoryInstitutionRepository::class);
        $this->config = m::mock(Config::class);
        $this->aggregator = new Aggregator($this->manager, $this->institutionRepository, $this->config);
    }

    protected function tearDown(): void
    {
        unset($this->aggregator);
        unset($this->manager);
        unset($this->institutionRepository);
        unset($this->config);
        parent::tearDown();
    }

    public function testAddKeywordsToEvents(): void
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

    public function testAddResourceTypesToEvents(): void
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

    public function testAddCompetencyObjectReferencesToEvents(): void
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

    public function testGetConsolidatedObjectivesMap(): void
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

    public function testGetFailsIfReportHasNoProgram(): void
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
     * @todo Implement this monster of a test. [ST 2018/07/18]
     */
    public function testGetData(): void
    {
        $this->markTestIncomplete('to be implemented.');
    }
}
