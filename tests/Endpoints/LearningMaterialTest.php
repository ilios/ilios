<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\LearningMaterialStatusInterface;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\LearningMaterialData;
use App\Tests\ReadWriteEndpointTest;

/**
 * LearningMaterial API endpoint Test.
 * @group api_4
 * @group time-sensitive
 */
class LearningMaterialTest extends ReadWriteEndpointTest
{
    private const UNBLANKED_ATTRIBUTES = [
        'id',
        'title',
        'uploadDate',
        'userRole',
        'status',
        'owningUser',
        'sessionLearningMaterials',
        'courseLearningMaterials',
        'copyrightPermission'
    ];
    protected string $testName =  'learningMaterials';

    protected function getFixtures(): array
    {
        return [
            LoadLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadOfferingData::class,
            LoadSessionData::class,
            LoadCourseData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'title' => ['title', 'a document'],
            'description' => ['description', 'lorem ipsum'],
            'nullDescription' => ['description', null],
            'originalAuthor' => ['originalAuthor', 'someone'],
            'userRole' => ['userRole', 2],
            'status' => ['status', LearningMaterialStatusInterface::IN_DRAFT],
            'owningUser' => ['owningUser', 2],
            'sessionLearningMaterials' => ['sessionLearningMaterials', [2], $skipped = true],
            'courseLearningMaterials' => ['courseLearningMaterials', [1], $skipped = true],
            'citation' => ['citation', 'dev/null'],
            'copyrightPermission' => ['copyrightPermission', false],
            'copyrightRationale' => ['copyrightRationale', 'fair use'],
            'link' => ['link', 'http://lorem.ipsum', $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'uploadDate' => ['uploadDate', 1, 99],
            'fileName' => ['uploadDate', 1, 'some text'],
            'mimeType' => ['uploadDate', 1, 'other text'],
            'filesize' => ['uploadDate', 1, 1648856844],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
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

    public function graphQLFiltersToTest(): array
    {
        $filters = $this->filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];

        return $filters;
    }

    protected function getTimeStampFields(): array
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
            $uri = $response['absoluteFileUri'] ?? null;
            if ($uri) {
                $this->kernelBrowser->request(
                    'GET',
                    $uri
                );

                $response = $this->kernelBrowser->getResponse();

                $this->assertEquals(
                    Response::HTTP_OK,
                    $response->getStatusCode(),
                    'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 1000)
                );
                $this->assertEquals(
                    file_get_contents(LoadLearningMaterialData::TEST_FILE_PATH),
                    $response->getContent()
                );
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
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData);
    }

    /**
     * @dataProvider qsToTest
     */
    public function testFindByQJsonApi(string $q, array $dataKeys)
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->jsonApiFilterTest($filters, $expectedData);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'lm', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffset()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0];
        $this->filterTest($filters, [$all[0]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffsetAndLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'lm', 'offset' => 3, 'limit' => 1];
        $this->filterTest($filters, [$all[3]]);
    }

    public function testFindByQWithOffsetAndLimitJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'lm', 'offset' => 3, 'limit' => 1];
        $this->jsonApiFilterTest($filters, [$all[3]]);
    }

    /**
     * @covers \App\Controller\API\LearningMaterials::getAll
     */
    public function testFindByQAsLearner()
    {
        $filters = ['q' => 'lm'];
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, $filters, 5);
        $this->assertEquals(count($filteredData), 10);
        foreach ($filteredData as $lm) {
            $this->assertEquals(count($lm), 9);
            foreach (self::UNBLANKED_ATTRIBUTES as $attr) {
                $this->assertTrue(array_key_exists($attr, $lm));
            }
        }
    }

    /**
     * @covers \App\Controller\API\LearningMaterials::getAll
     */
    public function testGetAllAsLearner()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, [], 5);
        $this->assertEquals(count($filteredData), 10);
        foreach ($filteredData as $lm) {
            $this->assertEquals(count($lm), 9);
            foreach (self::UNBLANKED_ATTRIBUTES as $attr) {
                $this->assertTrue(array_key_exists($attr, $lm));
            }
        }
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
            'text/plain'
        );
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/upload',
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser),
            ['file' => $fakeTestFile]
        );
        $response = $this->kernelBrowser->getResponse();
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

        $uri = $response['absoluteFileUri'] ?? null;
        $this->kernelBrowser->request(
            'GET',
            $uri
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 1000)
        );
        $this->assertEquals(file_get_contents($fakeTestFileDir . '/TESTFILE.txt'), $response->getContent());
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

    public function testPostLearningMaterialFileWithoutFile()
    {
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createFile();
        $data['fileHash'] = 'iamnotreal';
        $data['filename'] = 'We can only understand what we can perceive.gif';

        $data['mimetype'] = 'text/x-php';
        $data['filesize'] = '33M';
        $this->badPostTest($data);
    }

    /**
     * Ensure when LMs are sideloaded they have a correct URL path
     */
    public function testSideLoadedFileUrl()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $data = $all[2];
        $this->assertEquals('thirdlm', $data['title']);
        $this->assertEquals('testfile.txt', $data['filename']);
        $id = (string) $data['id'];
        $includes = $this->getJsonApiIncludeContent(
            'sessions',
            '3',
            'learningMaterials.learningMaterial'
        );
        $lms = array_filter($includes, fn(object $obj) => $obj->id === $id && $obj->type === 'learningMaterials');
        $lm = array_shift($lms);
        $this->assertEquals('thirdlm', $lm->attributes->title);
        $this->assertObjectHasAttribute('absoluteFileUri', $lm->attributes);
        $this->assertNotEmpty($lm->attributes->absoluteFileUri);
        $this->kernelBrowser->request(
            'GET',
            $lm->attributes->absoluteFileUri
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 1000)
        );

        $this->assertEquals(
            file_get_contents(LoadLearningMaterialData::TEST_FILE_PATH),
            $response->getContent()
        );
    }
}
