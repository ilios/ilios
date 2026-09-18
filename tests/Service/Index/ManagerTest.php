<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use App\Service\Index\Manager;
use App\Service\Index\Mesh;
use App\Service\Index\Users;
use App\Tests\TestCase;
use OpenSearch\Client;
use OpenSearch\Namespaces\IndicesNamespace;
use OpenSearch\Namespaces\IngestNamespace;
use Mockery as m;

final class ManagerTest extends TestCase
{
    private m\MockInterface $client;
    private m\MockInterface $config;
    private m\MockInterface $curriculumIndex;

    public function setUp(): void
    {
        parent::setUp();
        $this->curriculumIndex = m::mock(Curriculum::class);
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
        $this->config->shouldReceive('get')
            ->with('primaryLanguageOfInstruction')
            ->andReturn(null);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->curriculumIndex);
        unset($this->client);
        unset($this->config);
    }

    public function testSetup(): void
    {
        $obj1 = new Manager($this->curriculumIndex, $this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Manager($this->curriculumIndex, $this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testDropWorksWithoutSearch(): void
    {
        $this->expectNotToPerformAssertions();
        $obj = new Manager($this->curriculumIndex, $this->config, null);
        $obj->drop();
    }

    public function testDrop(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, $this->client);

        $indices = m::mock(IndicesNamespace::class);
        $this->client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('exists')
            ->with(['index' => Users::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => Mesh::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => Curriculum::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => LearningMaterials::INDEX])
            ->andReturn(true);

        $indices->shouldReceive('exists')
            ->with(['index' => 'ilios-public-curriculum'])
            ->andReturn(false);
        $indices->shouldReceive('exists')
            ->with(['index' => 'ilios-public-mesh'])
            ->andReturn(false);
        $indices->shouldReceive('exists')
            ->with(['index' => 'ilios-private-users'])
            ->andReturn(false);

        $indices->shouldReceive('delete')
            ->with(['index' => Users::INDEX])
            ->once();
        $indices->shouldReceive('delete')
            ->with(['index' => Mesh::INDEX])
            ->once();
        $indices->shouldReceive('delete')
            ->with(['index' => Curriculum::INDEX])
            ->once();
        $indices->shouldReceive('delete')
            ->with(['index' => LearningMaterials::INDEX])
            ->once();

        $obj->drop();
    }

    public function testCreateWorksWithoutSearch(): void
    {
        $this->expectNotToPerformAssertions();
        $obj = new Manager($this->curriculumIndex, $this->config, null);
        $obj->create();
    }

    public function testCreate(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, $this->client);

        $indices = m::mock(IndicesNamespace::class);
        $ingest = m::mock(IngestNamespace::class);
        $this->client->shouldReceive('indices')->andReturn($indices);
        $this->client->shouldReceive('ingest')->andReturn($ingest);

        $this->curriculumIndex->shouldReceive('getMapping')
            ->once()
            ->andReturn(['test' => 'mapping']);

        $indices->shouldReceive('create')
            ->with([
                'index' => Users::INDEX,
                'body' => Users::getMapping(),
            ])
            ->once();
        $indices->shouldReceive('create')
            ->with([
                'index' => Mesh::INDEX,
                'body' => Mesh::getMapping(),
            ])
            ->once();
        $indices->shouldReceive('create')
            ->with([
                'index' => Curriculum::INDEX,
                'body' => ['test' => 'mapping'],
            ])
            ->once();
        $indices->shouldReceive('create')
            ->with([
                'index' => LearningMaterials::INDEX,
                'body' => LearningMaterials::getMapping(),
            ])
            ->once();

        $ingest->shouldReceive('putPipeline')
            ->with(Users::getPipeline())
            ->once();
        $ingest->shouldReceive('putPipeline')
            ->with(Curriculum::getPipeline())
            ->once();

        $obj->create();
    }

    public function testHasBeenCreatedWithoutSearch(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, null);
        $this->assertFalse($obj->hasBeenCreated());
    }

    public function testHasBeenCreatedWhenAllIndexesExist(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, $this->client);

        $indices = m::mock(IndicesNamespace::class);
        $this->client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('exists')
            ->with(['index' => Users::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => Mesh::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => Curriculum::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => LearningMaterials::INDEX])
            ->andReturn(true);

        $this->assertTrue($obj->hasBeenCreated());
    }

    public function testHasBeenCreatedWhenOneIndexMissing(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, $this->client);

        $indices = m::mock(IndicesNamespace::class);
        $this->client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('exists')
            ->with(['index' => Users::INDEX])
            ->andReturn(true);
        $indices->shouldReceive('exists')
            ->with(['index' => Mesh::INDEX])
            ->andReturn(false); // This one is missing

        $this->assertFalse($obj->hasBeenCreated());
    }

    public function testHasBeenCreatedWhenAllIndexesMissing(): void
    {
        $obj = new Manager($this->curriculumIndex, $this->config, $this->client);

        $indices = m::mock(IndicesNamespace::class);
        $this->client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('exists')
            ->with(['index' => Users::INDEX])
            ->andReturn(false);

        $this->assertFalse($obj->hasBeenCreated());
    }
}
