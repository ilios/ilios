<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryReport;
use App\Service\CurriculumInventory\Manager;
use App\Repository\CurriculumInventoryReportRepository;
use DateTime;
use Mockery as m;
use App\Tests\TestCase;

/**
 * Class ManagerTest
 * @package App\Tests\Service\CurriculumInventory
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Service\CurriculumInventory\Manager::class)]
class ManagerTest extends TestCase
{
    protected m\MockInterface $repository;
    protected Manager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(CurriculumInventoryReportRepository::class);
        $this->manager = new Manager($this->repository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->manager);
    }

    public function testGetEventReferencesForSequenceBlocks(): void
    {
        $report = new CurriculumInventoryReport();

        $eventIds = [1, 2, 3, 4];

        $event1 = ['id' => 10, 'event_id' => 1];
        $event2 = ['id' => 20, 'event_id' => 2];
        $event3 = ['id' => 30, 'event_id' => 3];
        $event4 = ['id' => 10, 'event_id' => 4];

        $this->repository
            ->shouldReceive('getEventReferencesForSequenceBlocks')
            ->with($report, $eventIds)
            ->andReturn(
                [$event1, $event2, $event3, $event4]
            );

        $events = $this->manager->getEventReferencesForSequenceBlocks($report, $eventIds);
        $this->assertEquals(3, count($events));
        $this->assertEquals(2, count($events[10]));
        $this->assertEquals(1, count($events[20]));
        $this->assertEquals(1, count($events[30]));
        $this->assertEquals($event1, $events[10][0]);
        $this->assertEquals($event4, $events[10][1]);
        $this->assertEquals($event2, $events[20][0]);
        $this->assertEquals($event3, $events[30][0]);
    }

    public function testGetCompetencyObjectReferencesForSequenceBlocks(): void
    {
        $report = new CurriculumInventoryReport();

        $programObjective1 = ['id' => 1, 'course_objective_id' => null, 'program_objective_id' => 20];
        $programObjective2 = ['id' => 1, 'course_objective_id' => null, 'program_objective_id' => 10];
        $programObjective3 = ['id' => 2, 'course_objective_id' => null, 'program_objective_id' => 30];
        $programObjective4 = ['id' => 1, 'course_objective_id' => null, 'program_objective_id' => 40];

        $courseObjective1 = ['id' => 1, 'course_objective_id' => 12, 'program_objective_id' => null];
        $courseObjective2 = ['id' => 1, 'course_objective_id' => 22, 'program_objective_id' => null];
        $courseObjective3 = ['id' => 4, 'course_objective_id' => 32, 'program_objective_id' => null];

        $consolidatedProgramObjectivesMap = [40 => 10];

        $this->repository
            ->shouldReceive('getCompetencyObjectReferencesForSequenceBlocks')
            ->with($report)
            ->andReturn(
                [
                    $programObjective1,
                    $programObjective2,
                    $programObjective3,
                    $programObjective4,
                    $courseObjective1,
                    $courseObjective2,
                    $courseObjective3,
                ]
            );

        $refs = $this->manager->getCompetencyObjectReferencesForSequenceBlocks(
            $report,
            $consolidatedProgramObjectivesMap
        );

        $this->assertEquals(3, count($refs));
        $this->assertTrue(array_key_exists(1, $refs));
        $this->assertTrue(array_key_exists(2, $refs));
        $this->assertTrue(array_key_exists(4, $refs));
        $courseObjectives = $refs[1]['course_objectives'];
        $programObjectives = $refs[1]['program_objectives'];
        sort($courseObjectives);
        sort($programObjectives);
        $this->assertEquals(2, count($courseObjectives));
        $this->assertEquals(2, count($programObjectives));
        $this->assertEquals([12, 22], $courseObjectives);
        $this->assertEquals([10, 20], $programObjectives);
        $courseObjectives = $refs[2]['course_objectives'];
        $programObjectives = $refs[2]['program_objectives'];
        $this->assertEquals(0, count($courseObjectives));
        $this->assertEquals(1, count($programObjectives));
        $this->assertEquals([30], $programObjectives);
        $courseObjectives = $refs[4]['course_objectives'];
        $programObjectives = $refs[4]['program_objectives'];
        $this->assertEquals(1, count($courseObjectives));
        $this->assertEquals(0, count($programObjectives));
        $this->assertEquals([32], $courseObjectives);
    }

    public function testGetCompetencyObjectReferencesForEvents(): void
    {
        $report = new CurriculumInventoryReport();

        $programObjective1 = [
            'event_id' => 1,
            'session_objective_id' => null,
            'course_objective_id' => null,
            'program_objective_id' => 20,
        ];
        $programObjective2 = [
            'event_id' => 1,
            'session_objective_id' => null,
            'course_objective_id' => null,
            'program_objective_id' => 10,
        ];
        $programObjective3 = [
            'event_id' => 2,
            'session_objective_id' => null,
            'course_objective_id' => null,
            'program_objective_id' => 30,
        ];
        $programObjective4 = [
            'event_id' => 1,
            'session_objective_id' => null,
            'course_objective_id' => null,
            'program_objective_id' => 40,
        ];

        $courseObjective1 = [
            'event_id' => 1,
            'session_objective_id' => null,
            'course_objective_id' => 12,
            'program_objective_id' => null,
        ];
        $courseObjective2 = [
            'event_id' => 1,
            'session_objective_id' => null,
            'course_objective_id' => 22,
            'program_objective_id' => null,
        ];
        $courseObjective3 = [
            'event_id' => 4,
            'session_objective_id' => null,
            'course_objective_id' => 32,
            'program_objective_id' => null,
        ];

        $sessionObjective1 = [
            'event_id' => 2,
            'session_objective_id' => 55,
            'course_objective_id' => null,
            'program_objective_id' => null,
        ];
        $sessionObjective2 = [
            'event_id' => 2,
            'session_objective_id' => 65,
            'course_objective_id' => null,
            'program_objective_id' => null,
        ];
        $sessionObjective3 = [
            'event_id' => 4,
            'session_objective_id' => 75,
            'course_objective_id' => null,
            'program_objective_id' => null,
        ];

        $consolidatedProgramObjectivesMap = [40 => 10];

        $eventIds = [1, 2, 3, 4];

        $this->repository
            ->shouldReceive('getCompetencyObjectReferencesForEvents')
            ->with($report, $eventIds)
            ->andReturn(
                [
                    $programObjective1,
                    $programObjective2,
                    $programObjective3,
                    $programObjective4,
                    $courseObjective1,
                    $courseObjective2,
                    $courseObjective3,
                    $sessionObjective1,
                    $sessionObjective2,
                    $sessionObjective3,
                ]
            );

        $refs = $this->manager->getCompetencyObjectReferencesForEvents(
            $report,
            $consolidatedProgramObjectivesMap,
            $eventIds
        );

        $this->assertEquals(3, count($refs));
        $ref = $refs[1];
        $this->assertEquals(0, count($ref['session_objectives']));
        $this->assertEquals(2, count($ref['course_objectives']));
        $this->assertTrue(in_array(12, $ref['course_objectives']));
        $this->assertTrue(in_array(22, $ref['course_objectives']));
        $this->assertEquals(2, count($ref['program_objectives']));
        $this->assertTrue(in_array(10, $ref['program_objectives']));
        $this->assertTrue(in_array(20, $ref['program_objectives']));

        $ref = $refs[2];
        $this->assertEquals(2, count($ref['session_objectives']));
        $this->assertTrue(in_array(55, $ref['session_objectives']));
        $this->assertTrue(in_array(65, $ref['session_objectives']));
        $this->assertEquals(0, count($ref['course_objectives']));
        $this->assertEquals(1, count($ref['program_objectives']));
        $this->assertTrue(in_array(30, $ref['program_objectives']));

        $ref = $refs[4];
        $this->assertEquals(1, count($ref['session_objectives']));
        $this->assertTrue(in_array(75, $ref['session_objectives']));
        $this->assertEquals(1, count($ref['course_objectives']));
        $this->assertTrue(in_array(32, $ref['course_objectives']));
        $this->assertEquals(0, count($ref['program_objectives']));
    }

    public function testGetSessionObjectivesToCourseObjectivesRelations(): void
    {
        $objectiveRelationships = [
            ['objective_id' => 10, 'course_objective_id' => 20],
            ['objective_id' => 11, 'course_objective_id' => 21],
        ];

        $courseObjectiveIds = array_column($objectiveRelationships, 'course_objective_id');
        $sessionObjectiveIds = array_column($objectiveRelationships, 'objective_id');

        $this->repository->shouldReceive('getSessionObjectivesToCourseObjectivesRelations')
            ->with($sessionObjectiveIds, $courseObjectiveIds)
            ->andReturn($objectiveRelationships);

        $rhett = $this->manager->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );

        $this->assertEquals(2, count($rhett['relations']));
        $this->assertEquals(['rel1' => 20, 'rel2' => 10], $rhett['relations'][0]);
        $this->assertEquals(['rel1' => 21, 'rel2' => 11], $rhett['relations'][1]);
        $this->assertEquals($sessionObjectiveIds, $rhett['session_objective_ids']);
        $this->assertEquals($courseObjectiveIds, $rhett['course_objective_ids']);
    }

    public function testGetCourseObjectivesToProgramObjectivesRelations(): void
    {
        $objectiveRelationships = [
            ['objective_id' => 10, 'program_objective_id' => 20],
            ['objective_id' => 11, 'program_objective_id' => 21],
            ['objective_id' => 12, 'program_objective_id' => 22],
        ];

        $consolidatedProgramObjectivesMap = [22 => 41];

        $programObjectiveIds = array_column($objectiveRelationships, 'program_objective_id');
        $courseObjectiveIds = array_column($objectiveRelationships, 'objective_id');

        $this->repository->shouldReceive('getCourseObjectivesToProgramObjectivesRelations')
            ->with($courseObjectiveIds, $programObjectiveIds)
            ->andReturn($objectiveRelationships);

        $rhett = $this->manager->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds,
            $consolidatedProgramObjectivesMap
        );

        $this->assertEquals(3, count($rhett['relations']));
        $this->assertEquals(['rel1' => 20, 'rel2' => 10], $rhett['relations'][0]);
        $this->assertEquals(['rel1' => 21, 'rel2' => 11], $rhett['relations'][1]);
        $this->assertEquals(['rel1' => 41, 'rel2' => 12], $rhett['relations'][2]);

        $this->assertEquals($courseObjectiveIds, $rhett['course_objective_ids']);
        $this->assertEquals([20, 21, 41], $rhett['program_objective_ids']);
    }

    public function testGetProgramObjectivesToPcrsRelations(): void
    {
        $objectiveRelationships = [
            ['objective_id' => 10, 'pcrs_id' => 'a'],
            ['objective_id' => 11, 'pcrs_id' => 'b'],
            ['objective_id' => 12, 'pcrs_id' => 'c'],
        ];

        $consolidatedProgramObjectivesMap = [12 => 41];

        $programObjectiveIds = array_column($objectiveRelationships, 'objective_id');
        $pcrsIds = array_column($objectiveRelationships, 'pcrs_id');

        $this->repository->shouldReceive('getProgramObjectivesToPcrsRelations')
            ->with($programObjectiveIds, $pcrsIds)
            ->andReturn($objectiveRelationships);

        $rhett = $this->manager->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds,
            $consolidatedProgramObjectivesMap
        );

        $this->assertEquals(2, count($rhett['relations']));
        $this->assertEquals(['rel1' => 10, 'rel2' => 'a'], $rhett['relations'][0]);
        $this->assertEquals(['rel1' => 11, 'rel2' => 'b'], $rhett['relations'][1]);

        $this->assertEquals([10, 11], $rhett['program_objective_ids']);
        $this->assertEquals(['a', 'b'], $rhett['pcrs_ids']);
    }

    public function testGetEventsFromIlmOnlySessions(): void
    {
        $report = new CurriculumInventoryReport();
        $excludedSessionIds = []; // doesn't matter here

        $rows = [
            ['event_id' => 10, 'hours' => 0],
            ['event_id' => 20, 'hours' => 1.5],
            ['event_id' => 30, 'hours' => .33],
        ];

        $this->repository->shouldReceive('getEventsFromIlmOnlySessions')
            ->with($report, $excludedSessionIds)
            ->andReturn($rows);

        $events = $this->manager->getEventsFromIlmOnlySessions($report, $excludedSessionIds);

        $this->assertEquals(3, count($events));
        $this->assertEquals(0, $events[10]['duration']);
        $this->assertEquals(90, $events[20]['duration']);
        $this->assertEquals(19, $events[30]['duration']);
    }

    public function testGetEventsFromOfferingsOnlySessions(): void
    {
        $report = new CurriculumInventoryReport();
        $sessionIds = [30];
        $excludedSessionIds = []; // doesn't matter here

        $rows = [
            ['event_id' => 10, 'startDate' => new DateTime('2018-01-01'), 'endDate' => new DateTime('2018-01-02')],
            ['event_id' => 10, 'startDate' => new DateTime('2018-01-02'), 'endDate' => new DateTime('2018-01-03')],
            [
                'event_id' => 20,
                'startDate' => DateTime::createFromFormat('Y-m-d H:i:s', '2017-08-31 00:00:00'),
                'endDate' => DateTime::createFromFormat('Y-m-d H:i:s', '2017-08-31 01:30:00'),
            ],
            ['event_id' => 30, 'startDate' => new DateTime('2018-01-01'), 'endDate' => new DateTime('2018-01-02')],
            ['event_id' => 30, 'startDate' => new DateTime('2018-01-01'), 'endDate' => new DateTime('2018-01-03')],
            ['event_id' => 40, 'startDate' => null],
        ];

        $this->repository->shouldReceive('getEventsFromOfferingsOnlySessions')
            ->with($report, $excludedSessionIds)
            ->andReturn($rows);

        $events = $this->manager->getEventsFromOfferingsOnlySessions($report, $sessionIds, $excludedSessionIds);
        $this->assertEquals(4, count($events));
        $this->assertEquals(2880, $events[10]['duration']);
        $this->assertEquals(90, $events[20]['duration']);
        $this->assertEquals(2880, $events[30]['duration']);
        $this->assertEquals(0, $events[40]['duration']);
    }

    public function testGetEventsFromIlmSessionsWithOfferings(): void
    {
        $report = new CurriculumInventoryReport();
        $sessionIds = [30];
        $excludedSessionIds = []; // doesn't matter here

        $rows = [
            [
                'event_id' => 10,
                'startDate' => new DateTime('2018-01-01'),
                'endDate' => new DateTime('2018-01-02'),
                'ilm_hours' => null,
            ],
            [
                'event_id' => 20,
                'startDate' => DateTime::createFromFormat('Y-m-d H:i:s', '2017-08-31 00:00:00'),
                'endDate' => DateTime::createFromFormat('Y-m-d H:i:s', '2017-08-31 01:30:00'),
                'ilm_hours' => null,
            ],
            [
                'event_id' => 30,
                'startDate' => new DateTime('2018-01-01'),
                'endDate' => new DateTime('2018-01-02'),
                'ilm_hours' => null,
            ],
            [
                'event_id' => 30,
                'startDate' => new DateTime('2018-01-01'),
                'endDate' => new DateTime('2018-01-03'),
                'ilm_hours' => null,
            ],
            ['event_id' => 40, 'startDate' => null, 'ilm_hours' => null],
            [
                'event_id' => 50,
                'startDate' => new DateTime('2018-01-01'),
                'endDate' => new DateTime('2018-01-02'),
                'ilm_hours' => null,
            ],
            [
                'event_id' => 50,
                'startDate' => new DateTime('2018-01-02'),
                'endDate' => new DateTime('2018-01-03'),
                'ilm_hours' => null,
            ],
            ['event_id' => 50, 'startDate' => null, 'ilm_hours' => 1.5],

        ];

        $this->repository->shouldReceive('getEventsFromIlmSessionsWithOfferings')
            ->with($report, $excludedSessionIds)
            ->andReturn($rows);

        $events = $this->manager->getEventsFromIlmSessionsWithOfferings($report, $sessionIds, $excludedSessionIds);
        $this->assertEquals(5, count($events));
        $this->assertEquals(1440, $events[10]['duration']);
        $this->assertEquals(90, $events[20]['duration']);
        $this->assertEquals(2880, $events[30]['duration']);
        $this->assertEquals(0, $events[40]['duration']);
        $this->assertEquals(2970, $events[50]['duration']);
    }
}
