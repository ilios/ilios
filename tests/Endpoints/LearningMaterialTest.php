<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\LearningMaterialStatusInterface;
use App\Tests\DataLoader\LearningMaterialData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\QEndpointTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * LearningMaterial API endpoint Test.
 * @group api_4
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Controller\API\LearningMaterials::class)]
class LearningMaterialTest extends AbstractReadWriteEndpoint
{
    use QEndpointTrait;

    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

    private const array UNBLANKED_ATTRIBUTES = [
        'id',
        'title',
        'uploadDate',
        'userRole',
        'status',
        'owningUser',
        'sessionLearningMaterials',
        'courseLearningMaterials',
        'copyrightPermission',
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

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'a document'],
            'description' => ['description', 'lorem ipsum'],
            'nullDescription' => ['description', null],
            'originalAuthor' => ['originalAuthor', 'someone'],
            'userRole' => ['userRole', 2],
            'status' => ['status', LearningMaterialStatusInterface::IN_DRAFT],
            'owningUser' => ['owningUser', 2],
            // 'sessionLearningMaterials' => ['sessionLearningMaterials', [2]], // skipped
            // 'courseLearningMaterials' => ['courseLearningMaterials', [1]], // skipped
            'citation' => ['citation', 'dev/null'],
            'copyrightPermission' => ['copyrightPermission', false],
            'copyrightRationale' => ['copyrightRationale', 'fair use'],
            // 'link' => ['link', 'http://lorem.ipsum'], // skipped
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'uploadDate' => ['uploadDate', 1, 99],
            'fileName' => ['uploadDate', 1, 'some text'],
            'mimeType' => ['uploadDate', 1, 'other text'],
            'filesize' => ['uploadDate', 1, 1648856844],
        ];
    }

    public static function filtersToTest(): array
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
            'link' => [[1], ['link' => 'https://example.com/example-file.txt']],
            'courses' => [[0, 1, 2, 4, 5, 6, 7, 8, 9], ['courses' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'instructors' => [[0, 2, 4, 5, 6, 7, 8, 9], ['instructors' => [1, 2]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'terms' => [[0, 2, 4, 5, 6, 7, 8, 9], ['terms' => [3]]],
            'meshDescriptors' => [[0, 1, 2, 4, 5, 6, 7, 8, 9], ['meshDescriptors' => ['abc1', 'abc2']]],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'fullCoursesThroughCourse' => [[0], ['fullCourses' => [4]]],
            'fullCoursesThroughSession' => [[2, 4, 5, 6, 7, 8, 9], ['fullCourses' => [2]]],
            'school' => [[0], ['schools' => 2]],
            'schools' => [[0], ['schools' => [2]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];
        unset($filters['school']);

        return $filters;
    }

    protected function getTimeStampFields(): array
    {
        return ['uploadDate'];
    }

    public static function qsToTest(): array
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

    protected function compareData(array $expected, array $result): void
    {
        unset($result['absoluteFileUri']);
        parent::compareData($expected, $result);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'lm', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]], $jwt);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffset(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0];
        $this->filterTest($filters, [$all[0]], $jwt);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffsetAndLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'lm', 'offset' => 3, 'limit' => 1];
        $this->filterTest($filters, [$all[3]], $jwt);
    }

    public function testFindByQWithOffsetAndLimitJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['title'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'lm', 'offset' => 3, 'limit' => 1];
        $this->jsonApiFilterTest($filters, [$all[3]], $jwt);
    }

    public function testFindByQAsLearner(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 5);
        $filters = ['q' => 'lm'];
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);
        $this->assertEquals(10, count($filteredData));
        foreach ($filteredData as $lm) {
            $this->assertEquals(9, count($lm));
            foreach (self::UNBLANKED_ATTRIBUTES as $attr) {
                $this->assertArrayHasKey($attr, $lm);
            }
        }
    }

    public function testGetAllAsLearner(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 5);
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, [], $jwt);
        $this->assertEquals(10, count($filteredData));
        foreach ($filteredData as $lm) {
            $this->assertEquals(9, count($lm));
            foreach (self::UNBLANKED_ATTRIBUTES as $attr) {
                $this->assertArrayHasKey($attr, $lm);
            }
        }
    }

    public function testPostLearningMaterialCitation(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createCitation();
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    public function testPostLearningMaterialLink(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createLink();
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    public function testPostLearningMaterialFile(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
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
            $this->createJwtForRootUser($this->kernelBrowser),
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
        $response = $this->postTest($data, $postData, $jwt);

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

    public function testPostBadLearningMaterialCitation(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalidCitation();
        $this->badPostTest($data, $jwt);
    }

    public function testPostBadLearningMaterialLink(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalidLink();
        $this->badPostTest($data, $jwt);
    }

    public function testPostLearningMaterialFileWithoutFile(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        /** @var LearningMaterialData $dataLoader */
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createFile();
        $data['fileHash'] = 'iamnotreal';
        $data['filename'] = 'We can only understand what we can perceive.gif';

        $data['mimetype'] = 'text/x-php';
        $data['filesize'] = '33M';
        $this->badPostTest($data, $jwt);
    }

    /**
     * Ensure when LMs are side-loaded they have a correct URL path
     */
    public function testSideLoadedFileUrl(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $data = $all[2];
        $this->assertEquals('thirdlm', $data['title']);
        $this->assertEquals('testfile.txt', $data['filename']);
        $id = (string) $data['id'];
        $includes = $this->getJsonApiIncludeContent(
            'sessions',
            '3',
            'learningMaterials.learningMaterial',
            $jwt
        );
        $lms = array_filter($includes, fn(object $obj) => $obj->id === $id && $obj->type === 'learningMaterials');
        $lm = array_shift($lms);
        $this->assertEquals('thirdlm', $lm->attributes->title);
        $this->assertTrue(property_exists($lm->attributes, 'absoluteFileUri'));
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

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $data = $this->getDataLoader()->getOne();
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_put',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_patch',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
    }

    protected function runGetAllTest(string $jwt): void
    {
        $responses = $this->getAllTest($jwt);
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
}
