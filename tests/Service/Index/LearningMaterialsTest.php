<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use App\Tests\TestCase;
use OpenSearch\Client;
use Exception;
use InvalidArgumentException;
use Mockery as m;

class LearningMaterialsTest extends TestCase
{
    private m\MockInterface | Client $client;
    private m\MockInterface | Config $config;
    private m\MockInterface | NonCachingIliosFileSystem $fs;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->fs = m::mock(NonCachingIliosFileSystem::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->config);
        unset($this->fs);
    }

    public function testSetup(): void
    {
        $obj1 = new LearningMaterials($this->fs, $this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new LearningMaterials($this->fs, $this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexThrowsWhenNotLearningMaterialDTO(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $this->expectException(InvalidArgumentException::class);
        $courses = [
            m::mock(LearningMaterialDTO::class),
            m::mock(self::class),
            m::mock(LearningMaterialDTO::class),
        ];
        $obj->index($courses);
    }

    public function testIndexThrowsWithoutSearch(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->index([m::mock(LearningMaterialDTO::class)]);
    }

    public function testIndex(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $mockDto = m::mock(LearningMaterialDTO::class);
        $mockDto->id = 1;
        $mockDto->relativePath = 'first';
        $mockDto->title = 'first title';
        $mockDto->description = 'first description';
        $mockDto->filename = 'first filename';

        $mockDto2 = m::mock(LearningMaterialDTO::class);
        $mockDto2->id = 2;
        $mockDto2->relativePath = 'second';
        $mockDto2->title = 'second title';
        $mockDto2->description = 'second description';
        $mockDto2->filename = 'second filename';

        $this->setupSkippable([1, 2], []);

        $this->fs->shouldReceive('getLearningMaterialTextPath')
            ->with('first')
            ->once()
            ->andReturn('first-text-path');

        $this->fs->shouldReceive('getLearningMaterialTextPath')
            ->with('second')
            ->once()
            ->andReturn('second-text-path');

        $this->fs->shouldReceive('getFileContents')
            ->with('first-text-path')
            ->once()
            ->andReturn('first content');

        $this->fs->shouldReceive('getFileContents')
            ->with('second-text-path')
            ->once()
            ->andReturn('second content');

        $this->client->shouldReceive('request')->once()->withArgs(function ($method, $uri, $data) {
            $this->validateRequest($method, $uri, $data, [
                [
                    'index' => [
                        '_index' => LearningMaterials::INDEX,
                        '_id' => 'lm_0_1',
                    ],
                ],
                [
                    'id' => 'lm_0_1',
                    'learningMaterialId' => 1,
                    'title' => 'first title',
                    'description' => 'first description',
                    'filename' => 'first filename',
                    'contents' => 'first content',
                ],
                [
                    'index' => [
                        '_index' => LearningMaterials::INDEX,
                        '_id' => 'lm_0_2',
                    ],
                ],
                [
                    'id' => 'lm_0_2',
                    'learningMaterialId' => 2,
                    'title' => 'second title',
                    'description' => 'second description',
                    'filename' => 'second filename',
                    'contents' => 'second content',
                ],
            ]);
            return true;
        })->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$mockDto, $mockDto2]);
    }

    public function testSkipsPreviouslyIndexed(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $mockDto = m::mock(LearningMaterialDTO::class);
        $mockDto->id = 1;
        $mockDto->relativePath = 'first';
        $mockDto->title = 'first title';
        $mockDto->description = 'first description';
        $mockDto->filename = 'first filename';

        $mockDto2 = m::mock(LearningMaterialDTO::class);
        $mockDto2->id = 2;
        $mockDto2->relativePath = 'second';
        $mockDto2->title = 'second title';
        $mockDto2->description = 'second description';
        $mockDto2->filename = 'second filename';

        $this->setupSkippable([1, 2], [1]);

        $this->fs->shouldReceive('getLearningMaterialTextPath')
            ->with('second')
            ->once()
            ->andReturn('second-text-path');

        $this->fs->shouldReceive('getFileContents')
            ->with('second-text-path')
            ->once()
            ->andReturn('second content');

        $this->client->shouldReceive('request')->once()->withArgs(function ($method, $uri, $data) {
            $this->validateRequest($method, $uri, $data, [
                [
                    'index' => [
                        '_index' => LearningMaterials::INDEX,
                        '_id' => 'lm_0_2',
                    ],
                ],
                [
                    'id' => 'lm_0_2',
                    'learningMaterialId' => 2,
                    'title' => 'second title',
                    'description' => 'second description',
                    'filename' => 'second filename',
                    'contents' => 'second content',
                ],
            ]);
            return true;
        })->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$mockDto, $mockDto2]);
    }

    public function testIndexDoesNotSkipIfForced(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $mockDto = m::mock(LearningMaterialDTO::class);
        $mockDto->id = 1;
        $mockDto->relativePath = 'first';
        $mockDto->title = 'first title';
        $mockDto->description = 'first description';
        $mockDto->filename = 'first filename';

        $mockDto2 = m::mock(LearningMaterialDTO::class);
        $mockDto2->id = 2;
        $mockDto2->relativePath = 'second';
        $mockDto2->title = 'second title';
        $mockDto2->description = 'second description';
        $mockDto2->filename = 'second filename';

        $this->setupSkippable([1, 2], [1]);

        $this->fs->shouldReceive('getLearningMaterialTextPath')
            ->with('first')
            ->once()
            ->andReturn('first-text-path');

        $this->fs->shouldReceive('getLearningMaterialTextPath')
            ->with('second')
            ->once()
            ->andReturn('second-text-path');

        $this->fs->shouldReceive('getFileContents')
            ->with('first-text-path')
            ->once()
            ->andReturn('first content');

        $this->fs->shouldReceive('getFileContents')
            ->with('second-text-path')
            ->once()
            ->andReturn('second content');

        $this->client->shouldReceive('request')->once()->withArgs(function ($method, $uri, $data) {
                $this->validateRequest($method, $uri, $data, [
                    [
                        'index' => [
                            '_index' => LearningMaterials::INDEX,
                            '_id' => 'lm_0_1',
                        ],
                    ],
                    [
                        'id' => 'lm_0_1',
                        'learningMaterialId' => 1,
                        'title' => 'first title',
                        'description' => 'first description',
                        'filename' => 'first filename',
                        'contents' => 'first content',
                    ],
                    [
                        'index' => [
                            '_index' => LearningMaterials::INDEX,
                            '_id' => 'lm_0_2',
                        ],
                    ],
                    [
                        'id' => 'lm_0_2',
                        'learningMaterialId' => 2,
                        'title' => 'second title',
                        'description' => 'second description',
                        'filename' => 'second filename',
                        'contents' => 'second content',
                    ],
                ]);
                return true;
        })->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$mockDto, $mockDto2], true);
    }

    protected function setupSkippable(array $materialIds, array $skippableIds): void
    {
        $ids = array_map(fn(int $id) => ["key" => $id], $skippableIds);
        $this->client->shouldReceive('search')->with([
            'index' => LearningMaterials::INDEX,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'terms' => [
                                    'learningMaterialId' => $materialIds,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'learningMaterialId' => [
                        'terms' => [
                            'field' => 'learningMaterialId',
                            'size' => 10000,
                        ],
                    ],
                ],
                'size' => 0,
            ],
        ])->andReturn(['errors' => false, 'took' => 1, "aggregations" => [
            "learningMaterialId" => ["buckets" => $ids],
        ]]);
    }

    public function testGetMapping(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $mapping = $obj->getMapping();
        $this->assertArrayHasKey('settings', $mapping);
        $this->assertArrayHasKey('mappings', $mapping);
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
