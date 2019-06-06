<?php

namespace App\Tests\Controller;

use App\Command\UpdateFrontendCommand;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
     * @var KernelBrowser
     */
    protected $kernelBrowser;

    /**
     * @var array
     */
    protected $testFiles;

    public function setUp()
    {
        $this->kernelBrowser = static::createClient();
        $container = $this->kernelBrowser->getContainer();
        $cacheDir = $container->getParameter('kernel.cache_dir');
        $this->assetsPath =  $cacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fileSystem = new Filesystem();
        $this->testFiles = [];
    }

    /**
     * Remove all mock objects
     */
    public function tearDown() : void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        foreach ($this->testFiles as $path) {
            $this->fileSystem->remove($path);
        }
        unset($this->fs);
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
        $this->kernelBrowser->request('GET', '/');
        $response = $this->kernelBrowser->getResponse();

        $this->assertContains('<title>Ilios</title>', $response->getContent());
        $this->assertContains(
            '<meta name=\'iliosconfig-error-capture-enabled\' content="false">',
            $response->getContent()
        );

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

        $this->kernelBrowser->request('GET', '/fakeTestFile');
        $response = $this->kernelBrowser->getResponse();

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
        $this->kernelBrowser->request('GET', '/fakeTestFile', [], [], [
            'HTTP_If-Modified-Since' => $now->format('r')
        ]);
        /** @var BinaryFileResponse $response */
        $response = $this->kernelBrowser->getResponse();

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
        $this->kernelBrowser->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->kernelBrowser->getResponse();


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

        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->kernelBrowser->getResponse();

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

        $this->kernelBrowser->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->kernelBrowser->getResponse();
        $gzipEtag = $response->getEtag();

        $this->kernelBrowser->request(
            'GET',
            '/'
        );
        $response = $this->kernelBrowser->getResponse();
        $uncompressedEtag = $response->getEtag();

        $this->assertEquals($gzipEtag, $uncompressedEtag);


        $this->kernelBrowser->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br', 'HTTP_IF_NONE_MATCH' => $uncompressedEtag]
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());

        $this->kernelBrowser->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => $gzipEtag]
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
    }

    public function testBinaryFileFromCacheIsTheSameInGzippedAndUnCompressed()
    {
        $path = $this->assetsPath . 'fakeTestFile';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, true);

        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->kernelBrowser->getResponse();
        $gzipEtag = $response->getEtag();

        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile'
        );
        $response = $this->kernelBrowser->getResponse();
        $uncompressedEtag = $response->getEtag();

        $this->assertEquals($gzipEtag, $uncompressedEtag);


        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br', 'HTTP_IF_NONE_MATCH' => $uncompressedEtag]
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());

        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile',
            [],
            [],
            ['HTTP_IF_NONE_MATCH' => $gzipEtag]
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
    }

    public function testPNGFile()
    {
        $path = $this->assetsPath . 'fakeTestFile.png';
        $string = file_get_contents(__FILE__);
        $this->setupTestFile($path, $string, false);

        $this->kernelBrowser->request(
            'GET',
            '/fakeTestFile.png',
            [],
            [],
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'Wrong Status Code');
        $lastModified = \DateTime::createFromFormat('U', filemtime($path));

        $this->assertEquals($lastModified, $response->getLastModified(), 'Wrong Modified Cache Header');
        $this->assertGreaterThan(0, strlen($response->getEtag()), 'Missing Cache Header');
        $this->assertEquals(
            'image/png',
            $response->headers->get('Content-Type'),
            'content type headers are correct' . var_export($response->getContent(), true)
        );
        $this->assertFalse($response->headers->has('Content-Encoding'));
        $content = $response->getContent();
        $this->assertEquals($string, $content);
    }

    public function testErrorCaptureConfiguration()
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
        $orig = $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'];
        $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'] = true;
        $this->setupTestFile($jsonPath, $json, false);
        $this->kernelBrowser->request('GET', '/');
        $response = $this->kernelBrowser->getResponse();

        $this->assertContains(
            '<meta name=\'iliosconfig-error-capture-enabled\' content="true">',
            $response->getContent()
        );
        $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'] = $orig;
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
