<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\IndexableCourse;
use App\Entity\DTO\CourseDTO;
use App\Repository\CourseRepository;
use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use App\Tests\TestCase;
use OpenSearch\Client;
use DateTime;
use Exception;
use Mockery as m;

final class CurriculumTest extends TestCase
{
    private m\MockInterface $client;
    private m\MockInterface $config;
    protected m\MockInterface | CourseRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(CourseRepository::class);
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->client);
        unset($this->config);
    }

    public function testSetup(): void
    {
        $obj1 = new Curriculum($this->repository, $this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Curriculum($this->repository, $this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexCoursesWorksWithoutSearch(): void
    {
        $obj = new Curriculum($this->repository, $this->config, null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->index([13], new DateTime());
    }

    public function testIndexCourses(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
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

        $this->repository
            ->shouldReceive('getCourseIndexesFor')->once()
            ->with([1, 2])
            ->andReturn([$course1, $course2]);

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
        $obj->index([1, 2], $stamp);
    }

    public function testSkipsPreviouslyIndexedCourses(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
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

        $this->repository
            ->shouldReceive('getCourseIndexesFor')->once()
            ->with([2])
            ->andReturn([$course2]);

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
        $obj->index([1, 2], $stamp);
    }

    public function testIndexCourseWithNoSessions(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockDto->id = 1;
        $course1->courseDTO = $mockDto;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([]);

        $stamp = new DateTime();
        $this->setupSkippable($stamp, [1], []);
        $this->repository
            ->shouldReceive('getCourseIndexesFor')->once()
            ->with([1])
            ->andReturn([$course1]);

        $this->client->shouldNotReceive('bulk');
        $obj->index([1], new DateTime());
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
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        //$results['aggregations']['courseId']['buckets']
        $this->client->shouldReceive('search')->once()->andReturn([
            'aggregations' => [
                'courseId' => [
                    'buckets' => [
                        ['key' => 1],
                        ['key' => 2],
                    ],
                ],
            ],
        ]);
        $courseIds = $obj->getAllCourseIds();
        $this->assertCount(2, $courseIds);
        $this->assertEquals([1, 2], $courseIds);
    }

    public function testGetAllSessionIds(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'aggregations' => [
                'sessionId' => [
                    'buckets' => [
                        ['key' => 1],
                        ['key' => 2],
                    ],
                ],
            ],
            'hits' => [
                'total' => [
                    'value' => 2,
                ],
            ],
        ]);
        $this->client->shouldReceive('search')->once()->andReturn([
            'aggregations' => [
                'sessionId' => [
                    'buckets' => [
                    ],
                ],
            ],
            'hits' => [
                'total' => [
                    'value' => 0,
                ],
            ],
        ]);
        $ids = $obj->getAllSessionIds();
        $this->assertCount(2, $ids);
        $this->assertEquals([1, 2], $ids);
    }

    public function testGetMapping(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        $mapping = $obj->getMapping();
        $this->assertArrayHasKey('settings', $mapping);
        $this->assertArrayHasKey('mappings', $mapping);
    }

    public function testGetPipeline(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        $pipeline = $obj->getPipeline();
        $this->assertArrayHasKey('id', $pipeline);
        $this->assertArrayHasKey('body', $pipeline);
        $this->assertEquals('curriculum', $pipeline['id']);
    }

    public function testSearchSortOf(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
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
            $this->assertEquals(10, $b['size']);
            $this->assertEquals(0, $b['offset']);
            $this->assertArrayHasKey('query', $b);
            $this->assertArrayHasKey('function_score', $b['query']);
            $this->assertArrayHasKey('query', $b['query']['function_score']);
            $this->assertArrayHasKey('bool', $b['query']['function_score']['query']);
            $this->assertCount(2, $b['query']['function_score']['query']['bool']);
            $this->assertArrayHasKey('must', $b['query']['function_score']['query']['bool']);
            $this->assertArrayHasKey('should', $b['query']['function_score']['query']['bool']);
            $this->assertCount(3, $b['query']['function_score']['query']['bool']['must']);

            [ $fields, $years, $schools ] =  $b['query']['function_score']['query']['bool']['must'];

            $this->assertArrayHasKey('bool', $fields);
            $this->assertArrayHasKey('should', $fields['bool']);
            $this->assertSame(['terms' => ['courseYear.year' => [2005, 2013]]], $years);
            $this->assertSame(['terms' => ['schoolId' => [6, 24]]], $schools);


            return true;
        })->andReturn(['hits' => ['hits' => []], 'aggregations' => ['courses' => ['value' => 11]], 'suggest' => []]);
        $results = $obj->search('test', 10, 0, [6, 24], [2005, 2013]);
        $this->assertArrayHasKey('courses', $results);
        $this->assertCount(0, $results['courses']);
        $this->assertArrayHasKey('totalCourses', $results);
        $this->assertEquals(11, $results['totalCourses']);
        $this->assertArrayHasKey('didYouMean', $results);
    }

    public function testSearchDidYouMean(): void
    {
        $obj = new Curriculum($this->repository, $this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => ['hits' => []],
            'aggregations' => ['courses' => ['value' => 0]],
            'suggest' => [
                [
                    [
                        'options' => [
                            [
                                'score' => 0.62405,
                                'text' => 'jayden',
                            ],
                            [
                                'score' => 0.2007,
                                'text' => 'jasper',
                            ],
                            [
                                'score' => 1.0,
                                'text' => 'jayden',
                            ],
                        ],
                    ],
                ],
            ]]);
        $results = $obj->search('test', 10, 0, [6, 24], [2005, 2013]);
        $this->assertArrayHasKey('didYouMean', $results);
        $this->assertEquals([
            'score' => 0.62405,
            'didYouMean' => 'jayden',
        ], $results['didYouMean']);
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
