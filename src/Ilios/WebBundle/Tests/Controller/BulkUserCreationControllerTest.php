<?php
namespace Ilios\WebBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use \SplFileObject;

/**
 * Class ConfigControllerTest
 * @package Ilios\WebBundle\Tests\Controller
 */
class BulkUserCreationControllerTest extends WebTestCase
{
    use JsonControllerTest;

    protected $fakeTestFileDir;
    protected $fakeTestFile;
    protected $fs;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$this->fs->exists($this->fakeTestFileDir)) {
            $this->fs->mkdir($this->fakeTestFileDir);
        }

        $line1 = ['Person', 'Test', 'M', '555-650-1234', 'test@example.com', '1234', '1234Other'];
        $line2 = ['bad'];

        $file = new SplFileObject($this->fakeTestFileDir . '/TESTFILE.txt', 'w');
        $file->fputcsv($line1, "\t");
        $file->fputcsv($line2, "\t");

        $this->fs->copy(__FILE__, $this->fakeTestFileDir . '/TESTFILE.txt');
        $this->fakeTestFile = new UploadedFile(
            $this->fakeTestFileDir . '/TESTFILE.txt',
            'TESTFILE.txt',
            'text/plain',
            filesize($this->fakeTestFileDir . '/TESTFILE.txt')
        );

        $this->loadFixtures([
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPermissionData',
        ]);

    }

    public function tearDown()
    {
        $this->fs->remove($this->fakeTestFileDir);
        unset($this->fs);
        unset($this->fakeTestFile);
    }

    public function testUpload()
    {
        $client = $this->createClient();

        $this->makeJsonRequest(
            $client,
            'POST',
            '/application/bulkusercreation',
            json_encode(['school' => 1]),
            $this->getAuthenticatedUserToken(),
            array('file' => $this->fakeTestFile)
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(count($data), 2, 'Data returned for each line in the file');
        $this->assertSame(
            $data[0],
            ['Person', 'Test', 'M', '555-650-1234', 'test@example.com', '1234', '1234Other', 'success', 5]
        );
        $this->assertSame($data[1], ['bad', 'error', 'not enough fields']);
    }

    public function testBadSchoolId()
    {
        $client = $this->createClient();

        $this->makeJsonRequest(
            $client,
            'POST',
            '/application/bulkusercreation',
            json_encode(['school' => 23]),
            $this->getAuthenticatedUserToken(),
            array('file' => $this->fakeTestFile)
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);

        $this->assertEquals(
            array('errors' => 'The school 23 was not found'),
            json_decode($response->getContent(), true)
        );
    }

    public function testMissingFile()
    {
        $client = $this->createClient();

        $this->makeJsonRequest(
            $client,
            'POST',
            '/application/bulkusercreation',
            json_encode(['school' => 1]),
            $this->getAuthenticatedUserToken()
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);

        $this->assertEquals(
            array('errors' => 'No file parameter was found in the request'),
            json_decode($response->getContent(), true)
        );
    }
}
