<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\OpenSearchBase;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use App\Tests\TestCase;
use OpenSearch\Client;
use Exception;
use InvalidArgumentException;
use Mockery as m;

final class LearningMaterialsTest extends TestCase
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
        $goodMock1 = m::mock(LearningMaterialDTO::class);
        $goodMock1->relativePath = 'trouble';
        $goodMock2 = m::mock(LearningMaterialDTO::class);
        $goodMock2->relativePath = 'skiziks';
        $materials = [
            $goodMock1,
            m::mock(CourseDTO::class),
            $goodMock2,
        ];
        $obj->index($materials);
    }

    public function testIndexThrowsWhenNotAFileTypeMaterial(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Material 56 has no relative path and cannot be indexed, probably not a file type material.'
        );
        $goodMock =  m::mock(LearningMaterialDTO::class);
        $goodMock->relativePath = 'foo';
        $badMock = m::mock(LearningMaterialDTO::class);
        $badMock->relativePath = null;
        $badMock->id = 56;
        $materials = [$goodMock, $badMock,];
        $obj->index($materials);
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
                        '_id' => 'lm_1',
                    ],
                ],
                [
                    'id' => 'lm_1',
                    'learningMaterialId' => 1,
                    'title' => 'first title',
                    'description' => 'first description',
                    'filename' => 'first filename',
                    'contents' => 'first content',
                ],
                [
                    'index' => [
                        '_index' => LearningMaterials::INDEX,
                        '_id' => 'lm_2',
                    ],
                ],
                [
                    'id' => 'lm_2',
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
                        '_id' => 'lm_2',
                    ],
                ],
                [
                    'id' => 'lm_2',
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
                            '_id' => 'lm_1',
                        ],
                    ],
                    [
                        'id' => 'lm_1',
                        'learningMaterialId' => 1,
                        'title' => 'first title',
                        'description' => 'first description',
                        'filename' => 'first filename',
                        'contents' => 'first content',
                    ],
                    [
                        'index' => [
                            '_index' => LearningMaterials::INDEX,
                            '_id' => 'lm_2',
                        ],
                    ],
                    [
                        'id' => 'lm_2',
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
                            'size' => OpenSearchBase::SIZE_LIMIT,
                        ],
                    ],
                ],
                'size' => OpenSearchBase::SIZE_LIMIT,
            ],
        ])->andReturn(['errors' => false, 'took' => 1, "aggregations" => [
            "learningMaterialId" => ["buckets" => $ids],
        ]]);
    }


    public function testGetAllIds(): void
    {
        $obj = new LearningMaterials($this->fs, $this->config, $this->client);
        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_source' => ['learningMaterialId' => 1]],
                    ['_source' => ['learningMaterialId' => 2]],
                ],
            ],
            '_scroll_id' => '123',
        ]);
        $this->client->shouldReceive('scroll')->once()->andReturn(['hits' => ['hits' => []]]);
        $this->client->shouldReceive('clearScroll')->once();
        $ids = $obj->getAllIds();
        $this->assertCount(2, $ids);
        $this->assertEquals([1, 2], $ids);
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
