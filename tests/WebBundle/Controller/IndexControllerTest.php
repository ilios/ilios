<?php

namespace Tests\WebBundle\Controller;

use Ilios\CliBundle\Command\UpdateFrontendCommand;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Filesystem\Filesystem;

class IndexControllerTest extends WebTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:update-frontend';
    const TEST_API_VERSION = '33.14-test';

    /**
     * @var m\Mock
     */
    protected $assetsPath;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $testFiles;

    public function setUp()
    {
        $this->client = static::createClient();
        /** @var MockerContainer $container */
        $container = $this->client->getContainer();
        $cacheDir = $container->getParameter('kernel.cache_dir');
        $this->assetsPath =  $cacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fileSystem = new Filesystem();
        $this->testFiles = [];
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        foreach ($this->testFiles as $path) {
            $this->fileSystem->remove($path);
        }
        unset($this->fs);
        unset($this->client);
        unset($this->fileSystem);
    }

    public function testIndex()
    {
        $jsonPath = $this->assetsPath . 'index.json';
        $json = json_encode([
            'meta' => [],
            'link' => [],
            'script' => [],
            'style' => [],
            'noScript' => [],
            'div' => [],
        ]);
        $this->setupTestFile($jsonPath, $json, false);
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();

        $this->assertContains('<title>Ilios</title>', $response->getContent());

        $this->assertTrue(
            $response->headers->getCacheControlDirective('no-cache'),
            'cache headers are correct'
        );
        $this->assertEquals(
            null,
            $response->headers->get('Content-Encoding'),
            'content encoding headers are correct'
        );
    }

    public function testABinaryFile()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, true);

        $this->client->request('GET', '/fakeTestFile');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'Wrong Status Code');
        $lastModified = \DateTime::createFromFormat('U', filemtime($path));

        $this->assertEquals($lastModified, $response->getLastModified(), 'Wrong Modified Cache Header');
        $this->assertGreaterThan(0, strlen($response->getEtag()), 'Missing Cache Header');
        $this->assertEquals(
            null,
            $response->headers->get('Content-Encoding'),
            'content encoding headers are correct'
        );
        $this->assertEquals($string, $response->getContent());
    }

    public function testCacheResponse()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, true);
        $now = new \DateTime();
        $this->client->request('GET', '/fakeTestFile', [], [], [
            'HTTP_If-Modified-Since' => $now->format('r')
        ]);
        /** @var BinaryFileResponse $response */
        $response = $this->client->getResponse();

        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
    }

    public function testGzippedWhenRequested()
    {
        $jsonPath = $this->assetsPath . 'index.json';
        $json = json_encode([
            'meta' => [],
            'link' => [],
            'script' => [],
            'style' => [],
            'noScript' => [],
            'div' => [],
        ]);
        $this->setupTestFile($jsonPath, $json, false);
        $this->client->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->client->getResponse();


        $this->assertTrue(
            $response->headers->getCacheControlDirective('no-cache'),
            'cache headers are correct'
        );
        $this->assertEquals(
            'gzip',
            $response->headers->get('Content-Encoding'),
            'content encoding headers are correct' . var_export($response->getContent(), true)
        );
        $content = $response->getContent();
        $inflatedContent = gzdecode($content);
        $this->assertContains('<title>Ilios</title>', $inflatedContent);
    }

    public function testGzippedBinaryFile()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, true);

        $this->client->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'Wrong Status Code');
        $lastModified = \DateTime::createFromFormat('U', filemtime($path));

        $this->assertEquals($lastModified, $response->getLastModified(), 'Wrong Modified Cache Header');
        $this->assertGreaterThan(0, strlen($response->getEtag()), 'Missing Cache Header');
        $this->assertEquals(
            'gzip',
            $response->headers->get('Content-Encoding'),
            'content encoding headers are correct' . var_export($response->getContent(), true)
        );
        $content = $response->getContent();
        $inflatedContent = gzdecode($content);
        $this->assertEquals($string, $inflatedContent);
    }

    public function testIndexFromCacheIsTheSameInGzippedAndUnCompressed()
    {
        $jsonPath = $this->assetsPath . 'index.json';
        $json = json_encode([
            'meta' => [],
            'link' => [],
            'script' => [],
            'style' => [],
            'noScript' => [],
            'div' => [],
        ]);
        $this->setupTestFile($jsonPath, $json, false);

        $this->client->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->client->getResponse();
        $gzipEtag = $response->getEtag();

        $this->client->request(
            'GET',
            '/'
        );
        $response = $this->client->getResponse();
        $uncompressedEtag = $response->getEtag();

        $this->assertEquals($gzipEtag, $uncompressedEtag);


        $this->client->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br', 'HTTP_IF_NONE_MATCH' => $uncompressedEtag]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());

        $this->client->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => $gzipEtag]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
    }

    public function testBinaryFileFromCacheIsTheSameInGzippedAndUnCompressed()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, true);

        $this->client->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->client->getResponse();
        $gzipEtag = $response->getEtag();

        $this->client->request(
            'GET',
            '/fakeTestFile'
        );
        $response = $this->client->getResponse();
        $uncompressedEtag = $response->getEtag();

        $this->assertEquals($gzipEtag, $uncompressedEtag);


        $this->client->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br', 'HTTP_IF_NONE_MATCH' => $uncompressedEtag]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());

        $this->client->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => $gzipEtag]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
    }

    protected function setupTestFile(string $path, string $contents, bool $compressContents)
    {
        $this->testFiles[] = $path;
        if ($compressContents) {
            $contents = gzencode($contents);
        }
        $this->fileSystem->dumpFile($path, $contents);
    }
}
