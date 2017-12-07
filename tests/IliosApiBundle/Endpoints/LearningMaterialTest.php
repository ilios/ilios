<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\LearningMaterialData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * LearningMaterial API endpoint Test.
 * @group api_4
 */
class LearningMaterialTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'learningMaterials';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'description' => ['description', $this->getFaker()->text],
            'originalAuthor' => ['originalAuthor', $this->getFaker()->text(80)],
            'userRole' => ['userRole', 2],
            'status' => ['status', LearningMaterialStatusInterface::IN_DRAFT],
            'owningUser' => ['owningUser', 2],
            'sessionLearningMaterials' => ['sessionLearningMaterials', [2], $skipped = true],
            'courseLearningMaterials' => ['courseLearningMaterials', [1], $skipped = true],
            'citation' => ['citation', $this->getFaker()->text],
            'copyrightPermission' => ['copyrightPermission', false],
            'copyrightRationale' => ['copyrightRationale', $this->getFaker()->text],
            'link' => ['link', $this->getFaker()->text, $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'uploadDate' => ['uploadDate', 1, 99],
            'fileName' => ['uploadDate', 1, $this->getFaker()->text],
            'mimeType' => ['uploadDate', 1, $this->getFaker()->text],
            'filesize' => ['uploadDate', 1, $this->getFaker()->randomDigitNotNull],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[2], ['title' => 'thirdlm']],
            'description' => [[0], ['description' => 'desc1']],
            'originalAuthor' => [[0], ['originalAuthor' => 'author1']],
            'userRole' => [[1, 2, 3, 4, 5, 6, 7, 8, 9], ['userRole' => 2]],
            'mimeTypeAndUserRole' => [[2], ['userRole' => 2, 'mimetype' => 'text/plain']],
            'finalized' => [[0], ['status' => LearningMaterialStatusInterface::FINALIZED]],
            'draft' => [[1], ['status' => LearningMaterialStatusInterface::IN_DRAFT]],
            'revised' => [[2, 3, 4, 5, 6, 7, 8, 9], ['status' => LearningMaterialStatusInterface::REVISED]],
            'owningUser' => [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], ['owningUser' => 1]],
            'sessionLearningMaterials' => [[2], ['sessionLearningMaterials' => [2]]],
            'courseLearningMaterials' => [[2], ['courseLearningMaterials' => [4]]],
            'citation' => [[0], ['citation' => 'citation1']],
            'copyrightPermission' => [[0, 2, 3, 4, 5, 6 ,7 ,8 ,9], ['copyrightPermission' => true]],
            'noCopyrightPermission' => [[1], ['copyrightPermission' => false]],
            'copyrightRationale' => [[2, 3, 4, 5, 6, 7, 8, 9], ['copyrightRationale' => 'i own it']],
            'filename' => [[2], ['filename' => 'testfile.txt']],
            'mimetype' => [[2], ['mimetype' => 'text/plain']],
            'filesize' => [[2, 3, 4, 5, 6, 7, 8, 9], ['filesize' => 1000]],
            'link' => [[1], ['link' => 'http://example.com/example-file.txt']],
            'courses' => [[0, 1, 2, 4, 5, 6, 7, 8, 9], ['courses' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'instructors' => [[0, 2, 4, 5, 6, 7, 8, 9], ['instructors' => [1, 2]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'terms' => [[0, 2, 4, 5, 6, 7, 8, 9], ['terms' => [3]]],
            'meshDescriptors' => [[0, 1, 2, 4, 5, 6, 7, 8, 9], ['meshDescriptors' => ['abc1', 'abc2']]],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'fullCoursesThroughCourse' => [[0], ['fullCourses' => [4]]],
            'fullCoursesThroughSession' => [[2, 4, 5, 6, 7, 8, 9], ['fullCourses' => [2]]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['uploadDate'];
    }

    public function qsToTest()
    {
        return [
            ['first', [0]],
            ['second', [1]],
            ['lm', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]],
            ['2', [1]],
            ['desc', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]],
            ['nada', []],
            ['author1', [0]],
        ];
    }

    protected function compareData(array $expected, array $result)
    {
        unset($result['absoluteFileUri']);
        return parent::compareData($expected, $result);
    }

    public function testGetAll()
    {
        $responses = $this->getAllTest();
        foreach ($responses as $response) {
            $uri = array_key_exists('absoluteFileUri', $response)?$response['absoluteFileUri']:null;
            if ($uri) {
                $this->client->request(
                    'GET',
                    $uri
                );

                $response = $this->client->getResponse();

                $this->assertJsonResponse($response, Response::HTTP_OK, false);
            }
        }
    }

    /**
     * @dataProvider qsToTest
     * @param $q
     * @param $dataKeys
     */
    public function testFindByQ($q, $dataKeys)
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData);
    }

    public function testPostLearningMaterialCitation()
    {
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createCitation();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testPostLearningMaterialLink()
    {
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createLink();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testPostLearningMaterialFile()
    {
        $fs = new Filesystem();
        $fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($fakeTestFileDir)) {
            $fs->mkdir($fakeTestFileDir);
        }
        $fs->copy(__FILE__, $fakeTestFileDir . '/TESTFILE.txt');
        $filesize = filesize($fakeTestFileDir . '/TESTFILE.txt');
        $fakeTestFile = new UploadedFile(
            $fakeTestFileDir . '/TESTFILE.txt',
            'TESTFILE.txt',
            'text/plain',
            $filesize
        );
        $this->makeJsonRequest(
            $this->client,
            'POST',
            '/upload',
            null,
            $this->getAuthenticatedUserToken(),
            ['file' => $fakeTestFile]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responseData = json_decode($response->getContent(), true);

        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createFile();
        $data['fileHash'] = $responseData['fileHash'];
        $data['filename'] = $responseData['filename'];

        $postData = $data;
        $data['mimetype'] = 'text/x-php';
        $data['filesize'] = $filesize;
        unset($data['fileHash']);
        $response = $this->postTest($data, $postData);

        $uri = array_key_exists('absoluteFileUri', $response)?$response['absoluteFileUri']:null;
        $this->client->request(
            'GET',
            $uri
        );

        $response = $this->client->getResponse();


        $this->assertJsonResponse($response, Response::HTTP_OK, false);
    }

    public function testPostBadLearningMaterialCitation()
    {
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalidCitation();
        $this->badPostTest($data);
    }

    public function testPostBadLearningMaterialLink()
    {
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalidLink();
        $this->badPostTest($data);
    }
}
