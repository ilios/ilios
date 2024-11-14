<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\Fixture\LoadUserData;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\TestableJsonController;

/**
 * @group controller
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Controller\UploadController::class)]
class UploadControllerTest extends WebTestCase
{
    use TestableJsonController;

    protected KernelBrowser $kernelBrowser;
    protected string $fakeTestFileDir;
    protected UploadedFile $fakeTestFile;
    protected FileSystem $fs;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadServiceTokenData::class,
            LoadUserData::class,
        ]);
        $this->fs = new Filesystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$this->fs->exists($this->fakeTestFileDir)) {
            $this->fs->mkdir($this->fakeTestFileDir);
        }
        $this->fs->copy(__FILE__, $this->fakeTestFileDir . '/TESTFILE.txt');
        $this->fakeTestFile = new UploadedFile(
            $this->fakeTestFileDir . '/TESTFILE.txt',
            'TESTFILE.txt',
            'text/plain'
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->fs->remove($this->fakeTestFileDir);
        unset($this->fs);
        unset($this->fakeTestFile);
        unset($this->kernelBrowser);
    }

    public function testUploadFile(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/upload',
            null,
            $this->createJwtForRootUser($this->kernelBrowser),
            ['file' => $this->fakeTestFile]
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['filename'], 'TESTFILE.txt');
        $this->assertSame($data['fileHash'], md5_file(__FILE__));
    }

    public function testAnonymousUploadFileDenied(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/upload',
            null,
            null,
            ['file' => $this->fakeTestFile]
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function testBadUpload(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/upload',
            null,
            $this->createJwtForRootUser($this->kernelBrowser),
            ['nofile' => $this->fakeTestFile]
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(
            $data['errors'],
            'Unable to find file in the request. The uploaded file may have exceeded the maximum allowed size'
        );
    }

    public function testFileUploadDeniedToServiceToken(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/upload',
            null,
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
            ['file' => $this->fakeTestFile]
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_FORBIDDEN);
    }
}
