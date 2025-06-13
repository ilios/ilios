<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\IndexableCourse;
use App\Entity\DTO\CourseDTO;
use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use App\Tests\TestCase;
use OpenSearch\Client;
use DateTime;
use Exception;
use InvalidArgumentException;
use Mockery as m;

final class CurriculumTest extends TestCase
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
            ['id' => 1, 'courseFileLearningMaterialIds' => [1, 3], 'sessionFileLearningMaterialIds' => []],
        ]);

        $course2 = m::mock(IndexableCourse::class);
        $mockDto2 = m::mock(CourseDTO::class);
        $mockDto2->id = 2;
        $course2->courseDTO = $mockDto2;
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 2, 'courseFileLearningMaterialIds' => [1], 'sessionFileLearningMaterialIds' => []],
            ['id' => 3, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => [2]],
        ]);

        $stamp = new DateTime();
        $this->setupSkippable($stamp, [1, 2], []);
        $this->client
            ->shouldReceive('request')->once()->withArgs(function ($method, $uri, $data) {
                $this->validateRequest($method, $uri, $data, [
                    [
                        'index' => [
                            '_index' => Curriculum::INDEX,
                            '_id' => 1,
                        ],
                    ],
                    [
                        'id' => 1,
                        'courseLearningMaterialAttachments' => ['first', 'third'],
                    ],
                    [
                        'index' => [
                            '_index' => Curriculum::INDEX,
                            '_id' => 2,
                        ],
                    ],
                    [
                        'id' => 2,
                        'courseLearningMaterialAttachments' => ['first'],
                    ],
                    [
                        'index' => [
                            '_index' => Curriculum::INDEX,
                            '_id' => 3,
                        ],
                    ],
                    [
                        'id' => 3,
                        'sessionLearningMaterialAttachments' => ['second'],
                    ],
                ]);
                return true;
            })
            ->andReturn(['errors' => false, 'took' => 1, 'items' => []]);

        $this->client->shouldReceive('count')->times(2)->andReturn([
            'count' => 2,
        ]);
        $this->client
            ->shouldReceive('search')->once()
            ->with([
                'index' => LearningMaterials::INDEX,
                'body' => [
                    'query' => [
                        'terms' => [
                            'learningMaterialId' => [1, 3],
                        ],
                    ],
                    '_source' => [
                        'id',
                        'learningMaterialId',
                        'contents',
                    ],
                    'sort' => ['learningMaterialId'],
                    'size' => 25,
                ],
            ])
            ->andReturn([
                'hits' => [
                    'hits' => [
                        [
                            '_source' => [
                                'contents' => 'first',
                                'learningMaterialId' => 1,
                                'id' => 'lm_1',
                            ],
                            'sort' => [1],
                        ],
                        [
                            '_source' => [
                                'contents' => 'third',
                                'learningMaterialId' => 3,
                                'id' => 'lm_3',
                            ],
                            'sort' => [3],
                        ],
                    ],
                ],
            ]);
        $this->client
            ->shouldReceive('search')->once()
            ->with([
                'index' => LearningMaterials::INDEX,
                'body' => [
                    'query' => [
                        'terms' => [
                            'learningMaterialId' => [1, 2],
                        ],
                    ],
                    '_source' => [
                        'id',
                        'learningMaterialId',
                        'contents',
                    ],
                    'sort' => ['learningMaterialId'],
                    'size' => 25,
                ],
            ])
            ->andReturn([
                'hits' => [
                    'hits' => [
                        [
                            '_source' => [
                                'contents' => 'first',
                                'learningMaterialId' => 1,
                                'id' => 'lm_1',
                            ],
                            'sort' => [1],
                        ],
                        [
                            '_source' => [
                                'contents' => 'second',
                                'learningMaterialId' => 2,
                                'id' => 'lm_2',
                            ],
                            'sort' => [2],
                        ],
                    ],
                ],
            ]);
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
        $this->client->shouldReceive('request')->once()->withArgs(function ($method, $uri, $data) {
            $this->validateRequest($method, $uri, $data, [
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 2,
                    ],
                ],
                [
                    'id' => 2,
                ],
            ]);
            return true;
        })->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
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

    public function testGetAllCourseIds(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_source' => ['courseId' => 1]],
                    ['_source' => ['courseId' => 2]],
                ],
            ],
            '_scroll_id' => '123',
        ]);
        $this->client->shouldReceive('scroll')->once()->andReturn(['hits' => ['hits' => []]]);
        $this->client->shouldReceive('clearScroll')->once();
        $courseIds = $obj->getAllCourseIds();
        $this->assertCount(2, $courseIds);
        $this->assertEquals([1, 2], $courseIds);
    }

    public function testGetAllSessionIds(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_source' => ['sessionId' => 1]],
                    ['_source' => ['sessionId' => 2]],
                ],
            ],
            '_scroll_id' => '123',
        ]);
        $this->client->shouldReceive('scroll')->once()->andReturn(['hits' => ['hits' => []]]);
        $this->client->shouldReceive('clearScroll')->once();
        $ids = $obj->getAllSessionIds();
        $this->assertCount(2, $ids);
        $this->assertEquals([1, 2], $ids);
    }

    public function testGetMapping(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $mapping = $obj->getMapping();
        $this->assertArrayHasKey('settings', $mapping);
        $this->assertArrayHasKey('mappings', $mapping);
    }

    public function testGetPipeline(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $pipeline = $obj->getPipeline();
        $this->assertArrayHasKey('id', $pipeline);
        $this->assertArrayHasKey('body', $pipeline);
        $this->assertEquals('curriculum', $pipeline['id']);
    }

    public function testSearchSortOf(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $this->client->shouldReceive('search')->once()->withArgs(function ($params) {
            $this->assertArrayHasKey('index', $params);
            $this->assertEquals(Curriculum::INDEX, $params['index']);
            $this->assertArrayHasKey('body', $params);
            $b =  $params['body'];
            $this->assertArrayHasKey('_source', $b);
            $this->assertEquals([
                'courseId', 'courseTitle', 'courseYear', 'sessionId', 'sessionTitle', 'school',
            ], $b['_source']);

            $this->assertArrayHasKey('sort', $b);
            $this->assertEquals('_score', $b['sort']);
            $this->assertArrayHasKey('size', $b);
            $this->assertEquals(25, $b['size']);
            $this->assertArrayHasKey('query', $b);
            $this->assertArrayHasKey('function_score', $b['query']);

            return true;
        })->andReturn(['hits' => ['hits' => []], 'suggest' => []]);
        $results = $obj->search('test', false);
        $this->assertArrayHasKey('autocomplete', $results);
        $this->assertCount(0, $results['autocomplete']);
        $this->assertArrayHasKey('courses', $results);
        $this->assertCount(0, $results['courses']);
    }

    public function testSearchOnlySuggest(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [],
            ],
            'suggest' => [
                [[
                    'options' => [
                        [
                            'text' => 'suggested',
                        ],
                        [
                            'text' => 'suggester',
                        ],
                    ],
                ]],
            ],
        ]);
        $results = $obj->search('test', true);
        $this->assertArrayHasKey('autocomplete', $results);
        $this->assertEquals(['suggested', 'suggester'], $results['autocomplete']);
        $this->assertArrayHasKey('courses', $results);
        $this->assertCount(0, $results['courses']);
    }

    protected function validateRequest(
        string $method,
        string $uri,
        array $data,
        array $expected,
    ): void {
        $this->assertEquals('POST', $method);
        $this->assertEquals('/_bulk', $uri);
        $this->assertArrayHasKey('body', $data);
        $this->assertArrayHasKey('options', $data);
        $this->assertEquals(['headers' => ['Content-Encoding' => 'gzip']], $data['options']);
        $body = gzdecode($data['body']);
        $arr = array_map(fn ($item) => json_decode($item, true), explode("\n", $body));
        $filtered = array_filter($arr, 'is_array');
        $this->assertCount(count($expected), $filtered);
        $this->assertEquals($expected, $filtered);
    }
}
