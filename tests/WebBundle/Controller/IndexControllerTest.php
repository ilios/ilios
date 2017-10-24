<?php

namespace Tests\WebBundle\Controller;

use Ilios\CliBundle\Command\UpdateFrontendCommand;
use Ilios\CoreBundle\Service\Filesystem;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

class IndexControllerTest extends WebTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:update-frontend';
    const TEST_API_VERSION = '33.14-test';

    /**
     * @var m\Mock
     */
    protected $mockFileSystem;
    protected $assetsPath;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        /** @var MockerContainer $container */
        $container = $this->client->getContainer();
        $this->mockFileSystem = $container->mock(Filesystem::class, Filesystem::class);
        $cacheDir = $container->getParameter('kernel.cache_dir');
        $this->assetsPath =  $cacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fileSystem = new SymfonyFileSystem();
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->fs);
        unset($this->client);
        unset($this->fileSystem);
    }

    public function testIndex()
    {
        $jsonPath = $this->assetsPath . 'index.json';
        $this->mockFileSystem->shouldReceive('exists')->with($jsonPath)->once()->andReturn(true);
        $this->mockFileSystem->shouldReceive('readFile')->with($jsonPath)->once()->andReturn(json_encode([
            'meta' => [],
            'link' => [],
            'script' => [],
            'style' => [],
            'noScript' => [],
            'div' => [],
        ]));
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();

        //ensure we have correct cache headers
        $this->assertTrue($response->headers->getCacheControlDirective('no-cache'));
    }

    public function testABinaryFile()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $this->fileSystem->copy(__FILE__, $path);
        $this->mockFileSystem->shouldReceive('exists')->with($path)->once()->andReturn(true);
        $this->client->request('GET', '/fakeTestFile');
        /** @var BinaryFileResponse $response */
        $response = $this->client->getResponse();

        $this->assertEquals($path, $response->getFile()->getRealPath(), 'Got File');
        $this->assertEquals(200, $response->getStatusCode(), 'Wrong Status Code');
        $lastModified = \DateTime::createFromFormat('U', filemtime($path));

        $this->assertEquals($lastModified, $response->getLastModified(), 'Wrong Modified Cache Header');
        $this->assertGreaterThan(0, strlen($response->getEtag()), 'Missing Cache Header');

        $this->fileSystem->remove($path);
    }

    public function testCacheResponse()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $this->fileSystem->copy(__FILE__, $path);
        $this->mockFileSystem->shouldReceive('exists')->with($path)->once()->andReturn(true);
        $now = new \DateTime();
        $this->client->request('GET', '/fakeTestFile', [], [], [
            'HTTP_If-Modified-Since' => $now->format('r')
        ]);
        /** @var BinaryFileResponse $response */
        $response = $this->client->getResponse();

        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');

        $this->fileSystem->remove($path);
    }
}
