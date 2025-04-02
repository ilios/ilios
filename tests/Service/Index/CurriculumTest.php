<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\IndexableCourse;
use App\Entity\DTO\CourseDTO;
use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use OpenSearch\Client;
use DateTime;
use Exception;
use InvalidArgumentException;
use Mockery as m;

class CurriculumTest extends TestCase
{
    private m\MockInterface $client;
    private m\MockInterface $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->config);
    }

    public function testSetup(): void
    {
        $obj1 = new Curriculum($this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Curriculum($this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }


    public function testIndexCoursesThrowsWhenNotIndexableCourse(): void
    {
        $obj = new Curriculum($this->config, null);
        $this->expectException(InvalidArgumentException::class);
        $courses = [
            m::mock(IndexableCourse::class),
            m::mock(CourseDTO::class),
            m::mock(IndexableCourse::class),
        ];
        $obj->index($courses, new DateTime());
    }

    public function testIndexCoursesWorksWithoutSearch(): void
    {
        $obj = new Curriculum($this->config, null);
        $mockCourse = m::mock(IndexableCourse::class);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->index([$mockCourse], new DateTime());
    }

    public function testIndexCourses(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockDto->id = 1;
        $course1->courseDTO = $mockDto;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 1, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $course2 = m::mock(IndexableCourse::class);
        $mockDto2 = m::mock(CourseDTO::class);
        $mockDto2->id = 2;
        $course2->courseDTO = $mockDto2;
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 2, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
            ['id' => 3, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $stamp = new DateTime();
        $this->setupSkippable($stamp, [1, 2], []);
        $this->client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 1,
                    ],
                ],
                [
                    'id' => 1,
                ],
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 2,
                    ],
                ],
                [
                    'id' => 2,
                ],
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 3,
                    ],
                ],
                [
                    'id' => 3,
                ],
            ],
        ])->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$course1, $course2], $stamp);
    }

    public function testSkipsPreviouslyIndexedCourses(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockDto->id = 1;
        $course1->courseDTO = $mockDto;
        $course1->shouldNotReceive('createIndexObjects');

        $course2 = m::mock(IndexableCourse::class);
        $mockDto2 = m::mock(CourseDTO::class);
        $mockDto2->id = 2;
        $course2->courseDTO = $mockDto2;
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 2, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $stamp = new DateTime();
        $this->setupSkippable($stamp, [1, 2], [1]);
        $this->client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 2,
                    ],
                ],
                [
                    'id' => 2,
                ],
            ],
        ])->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$course1, $course2], $stamp);
    }

    public function testIndexCourseWithNoSessions(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockDto->id = 1;
        $course1->courseDTO = $mockDto;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([]);

        $stamp = new DateTime();
        $this->setupSkippable($stamp, [1], []);

        $this->client->shouldNotReceive('bulk');
        $obj->index([$course1], new DateTime());
    }

    protected function setupSkippable(DateTime $stamp, array $courseIds, array $skippableCourses): void
    {
        $ids = array_map(fn(int $id) => ["key" => $id], $skippableCourses);
        $this->client->shouldReceive('search')->once()->with([
            'index' => Curriculum::INDEX,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'range' => [
                                    'ingestTime' => [
                                        'gte' => $stamp->format('c'),
                                    ],
                                ],
                            ],
                            [
                                'terms' => [
                                    'courseId' => $courseIds,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'courseId' => [
                        'terms' => [
                            'field' => 'courseId',
                            'size' => 10000,
                        ],
                    ],
                ],
                'size' => 0,
            ],
        ])->andReturn(['errors' => false, 'took' => 1, "aggregations" => ["courseId" => ["buckets" => $ids]]]);
    }
}
