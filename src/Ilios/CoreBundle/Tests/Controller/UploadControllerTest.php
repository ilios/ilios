<?php
namespace Ilios\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use FOS\RestBundle\Util\Codes;

use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;

/**
 * Upload controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UploadControllerTest extends WebTestCase
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
        $this->fs->copy(__FILE__, $this->fakeTestFileDir . '/TESTFILE.txt');
        $this->fakeTestFile = new UploadedFile(
            $this->fakeTestFileDir . '/TESTFILE.txt',
            'TESTFILE.txt',
            'text/plain',
            filesize($this->fakeTestFileDir . '/TESTFILE.txt')
        );
    }

    public function tearDown()
    {
        $this->fs->remove($this->fakeTestFileDir);
        unset($this->fs);
        unset($this->fakeTestFile);
    }

    /**
     * @group controllers
     */
    public function testUploadFile()
    {
        $client = $this->createClient();
        
        $this->makeJsonRequest(
            $client,
            'POST',
            '/upload',
            null,
            $this->getAuthenticatedUserToken(),
            array('file' => $this->fakeTestFile)
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['filename'], 'TESTFILE.txt');
        $this->assertSame($data['fileHash'], md5_file(__FILE__));
    }

    /**
     * @group controllers
     */
    public function testBadUpload()
    {
        $client = $this->createClient();
        
        $this->makeJsonRequest(
            $client,
            'POST',
            '/upload',
            null,
            $this->getAuthenticatedUserToken(),
            array('nofile' => $this->fakeTestFile)
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);
        
        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['errors'], 'No file parameter was found in the request');
    }
}
