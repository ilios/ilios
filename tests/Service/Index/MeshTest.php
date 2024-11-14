<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Service\Config;
use App\Service\Index\Mesh;
use App\Tests\TestCase;
use OpenSearch\Client;
use Exception;
use Ilios\MeSH\Model\Descriptor;
use Mockery as m;

class MeshTest extends TestCase
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
        $obj1 = new Mesh($this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Mesh($this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexMeshDescriptorsWorksWithoutSearch(): void
    {
        $desc1 = m::mock(Descriptor::class);
        $desc1->shouldReceive('getConcepts')->once()->andReturn([]);
        $desc1->shouldReceive('getUi')->once()->andReturn('id');
        $desc1->shouldReceive('getName')->once()->andReturn('name');
        $desc1->shouldReceive('getAnnotation')->once()->andReturn('annt');
        $desc1->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi']);

        $obj = new Mesh($this->config, null);
        $desc2 = m::mock(Descriptor::class);
        $desc2->shouldReceive('getConcepts')->once()->andReturn([]);
        $desc2->shouldReceive('getUi')->once()->andReturn('id');
        $desc2->shouldReceive('getName')->once()->andReturn('name');
        $desc2->shouldReceive('getAnnotation')->once()->andReturn('annt');
        $desc2->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi']);

        $obj = new Mesh($this->config, null);
        $arr = [
            $desc1,
            $desc2,
        ];
        $this->assertTrue($obj->index($arr));
    }

    public function testIdsQueryThrowsExceptionWhenNotConfigured(): void
    {
        $obj = new Mesh($this->config, null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->idsQuery('');
    }
}
