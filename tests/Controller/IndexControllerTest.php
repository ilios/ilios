<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controller\IndexController;
use App\Command\UpdateFrontendCommand;
use App\Service\AuthenticationInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

#[Group('controller')]
#[CoversClass(IndexController::class)]
final class IndexControllerTest extends WebTestCase
{
    use MockeryPHPUnitIntegration;

    private const string BROWSER_USER_AGENT = 'Chrome';
    private const string CRAWLER_USER_AGENT = 'NetpeakCheckerBot';
    private const string JSON = '{"meta":[],"link":[],"script":[],"style":[],"noScript":[],"div":[]}';

    protected string $jsonPath;
    protected Filesystem $fileSystem;
    protected KernelBrowser $kernelBrowser;
    protected ContainerInterface $container;
    protected array $testFiles = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = static::createClient();
        $this->container = $this->kernelBrowser->getContainer();
        $projectDir = $this->container->getParameter('kernel.project_dir');
        $this->jsonPath = UpdateFrontendCommand::getActiveFrontendIndexPath($projectDir);
        $this->fileSystem = new Filesystem();
        $this->testFiles = [];
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->container);
        foreach ($this->testFiles as $path) {
            $this->fileSystem->remove($path);
        }
        unset($this->fileSystem);
        unset($this->testFiles);
    }

    public function testIndex(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->kernelBrowser->request('GET', '/');
        $response = $this->kernelBrowser->getResponse();

        $this->assertStringContainsString('<title>Ilios</title>', $response->getContent());
        $this->assertStringContainsString(
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

    public function testGzippedWhenRequested(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->makeRequest('/', ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']);
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
        $this->assertStringContainsString('<title>Ilios</title>', $inflatedContent);
    }

    public function testIndexFromCacheIsTheSameInGzippedAndUnCompressed(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->makeRequest('/', ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br']);

        $response = $this->kernelBrowser->getResponse();
        $gzipEtag = $response->getEtag();

        $this->kernelBrowser->request(
            'GET',
            '/'
        );
        $response = $this->kernelBrowser->getResponse();
        $uncompressedEtag = $response->getEtag();

        $this->assertEquals($gzipEtag, $uncompressedEtag);

        $this->makeRequest(
            '/',
            ['HTTP_ACCEPT_ENCODING' => 'deflate, gzip, br', 'HTTP_IF_NONE_MATCH' => $uncompressedEtag]
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
        $this->makeRequest('/', ['HTTP_IF_NONE_MATCH' => $gzipEtag]);

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(304, $response->getStatusCode(), 'Wrong Status Code');
        $this->assertEmpty($response->getContent());
    }

    public function testErrorCaptureConfiguration(): void
    {
        $orig = $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'];
        $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'] = true;
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->kernelBrowser->request('GET', '/');
        $response = $this->kernelBrowser->getResponse();

        $this->assertStringContainsString(
            '<meta name=\'iliosconfig-error-capture-enabled\' content="true">',
            $response->getContent()
        );
        $_ENV['ILIOS_ERROR_CAPTURE_ENABLED'] = $orig;
    }

    public function testRedirectForBrowsers(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->mockAuthenticationResponse(new RedirectResponse('/auth/login'));
        $this->makeRequest('/', ['HTTP_USER_AGENT' => self::BROWSER_USER_AGENT]);

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('/auth/login', $response->headers->get('Location'));
        $this->assertStringNotContainsString('<title>Ilios</title>', $response->getContent());
    }

    public function testProvideIndexForCrawlers(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->mockAuthenticationResponse(new RedirectResponse('/auth/login'));
        $this->makeRequest('/', ['HTTP_USER_AGENT' => self::CRAWLER_USER_AGENT]);

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNull($response->headers->get('Location'));
        $this->assertStringContainsString('<title>Ilios</title>', $response->getContent());
    }

    public function testProvideIndexForLtiAuth(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->mockAuthenticationResponse(new RedirectResponse('/auth/login'));
        $this->makeRequest('/lti-login', ['HTTP_USER_AGENT' => self::BROWSER_USER_AGENT]);

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNull($response->headers->get('Location'));
        $this->assertStringContainsString('<title>Ilios</title>', $response->getContent());
    }

    public function testProvideIndexForNonRedirectResponse(): void
    {
        $this->setupTestFile($this->jsonPath, self::JSON, false);
        $this->mockAuthenticationResponse(new Response());
        $this->makeRequest('/', ['HTTP_USER_AGENT' => self::BROWSER_USER_AGENT]);
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('<title>Ilios</title>', $response->getContent());
    }

    protected function setupTestFile(string $path, string $contents, bool $compressContents): void
    {
        $this->testFiles[] = $path;
        if ($compressContents) {
            $contents = gzencode($contents);
        }
        $this->fileSystem->dumpFile($path, $contents);
    }

    protected function makeRequest(string $url, array $headers): void
    {
        $this->kernelBrowser->request(
            'GET',
            $url,
            [],
            [],
            $headers,
        );
    }

    protected function mockAuthenticationResponse(Response $authenticationResponse): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('createAuthenticationResponse')->andReturn($authenticationResponse);
        $this->container->set(AuthenticationInterface::class, $authentication);
    }
}
