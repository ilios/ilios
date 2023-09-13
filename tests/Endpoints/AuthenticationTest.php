<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAlertData;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadPendingUserUpdateData;
use App\Tests\Fixture\LoadReportData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadUserData;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\UserData;

/**
 * Authentication API endpoint Test.
 * @group api_5
 */
class AuthenticationTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'authentications';
    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadAuthenticationData::class,
            LoadUserData::class,
            LoadAlertData::class,
            LoadCourseData::class,
            LoadLearningMaterialData::class,
            LoadInstructorGroupData::class,
            LoadLearnerGroupData::class,
            LoadIlmSessionData::class,
            LoadOfferingData::class,
            LoadPendingUserUpdateData::class,
            LoadSessionLearningMaterialData::class,
            LoadReportData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'username' => ['username', 'devnull'],
            'password' => ['password', 'geheimsache'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'invalidateTokenIssuedBefore' => ['invalidateTokenIssuedBefore', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
    {
        return [
            'user' => [[1], ['user' => 2]],
            'username' => [[1], ['username' => 'newuser']],
        ];
    }

    protected function createMany($count)
    {
        $userDataLoader = self::getContainer()->get(UserData::class);
        $users = $userDataLoader->createMany($count);
        $savedUsers = $this->postMany('users', 'users', $users);

        $dataLoader = $this->getDataLoader();

        $data = array_map(function ($user) use ($dataLoader) {
            $arr = $dataLoader->create();
            $arr['user'] = (string) $user['id'];
            $arr['username'] .= $user['id'];

            return $arr;
        }, $savedUsers);

        return $data;
    }

    protected function createBulkJsonApi($arr): object
    {
        $data = array_map(function (array $user) {
            $rhett = [
                'id' => (string) $user['user'],
                'type' => 'authentications',
                'attributes' => [
                    'username' => $user['username'],
                ],
                'relationships' => [
                    'user' => [
                        'data' => [
                            'id' => $user['user'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ];
            if (array_key_exists('password', $user)) {
                $rhett['attributes']['password'] = $user['password'];
            }

            return $rhett;
        }, $arr);

        return json_decode(json_encode(['data' => $data]), false);
    }

    protected function compareData(array $expected, array $result)
    {
        unset($expected['passwordHash']);
        unset($expected['password']);
        unset($expected['invalidateTokenIssuedBefore']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    protected function compareJsonApiData(array $expected, object $result)
    {
        $this->assertEquals($expected['user'], $result->id);
        $this->assertEquals($expected['username'], $result->attributes->username);
    }

    public function testPostMultipleAuthenticationWithEmptyPassword()
    {
        $data = $this->createMany(101);
        $data = array_map(function ($arr) {
            unset($arr['password']);
            return $arr;
        }, $data);
        $this->postManyTest($data);
    }

    public function testPostMultipleAuthenticationWithEmptyPasswordJsonApi()
    {
        $arr = $this->createMany(101);
        $arr = array_map(function ($item) {
            unset($item['password']);
            return $item;
        }, $arr);

        $data = $this->createBulkJsonApi($arr);
        $this->postManyJsonApiTest($data, $arr);
    }

    public function testPostAuthenticationWithEmptyPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['password']);

        $this->postTest($data, $data);
    }

    public function testPostAuthenticationWithEmptyPasswordJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        unset($arr['password']);
        $data = $dataLoader->createJsonApi($arr);

        $this->postJsonApiTest($data, $arr);
    }

    public function testPutAuthenticationWithNewUsernameAndPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['passwordHash']);
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';

        $this->putTest($data, $data, $data['user']);
    }

    public function testPutAuthenticationWithNewUsernameAndPasswordJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['passwordHash']);
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';
        $jsonApiData = $dataLoader->createJsonApi($data);

        $this->patchJsonApiTest($data, $jsonApiData);
    }

    public function testPostAuthenticationForUserWithNonPrimarySchool()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['user'] = '4';
        $user4 = parent::getOne('users', 'users', 4);
        $this->assertSame($user4['school'], 2, 'User #4 should be in school 2 or this test is garbage');

        $this->postTest($data, $data);
    }

    public function testPostAuthenticationWithNoUsernameOrPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['username']);
        unset($data['password']);

        $this->postTest($data, $data);
    }

    public function testPostAuthenticationWithNoUsernameOrPasswordJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        unset($arr['username']);
        unset($arr['password']);
        $data = $dataLoader->createJsonApi($arr);

        $this->postJsonApiTest($data, $arr);
    }

    public function test3396PutAuthenticationWithInvalidation()
    {
        $dataLoader = $this->getDataLoader();
        $allData = $dataLoader->getAll();
        $this->assertArrayHasKey(2, $allData);
        $data = $allData[2];
        $this->assertArrayHasKey('invalidateTokenIssuedBefore', $data);

        unset($data['passwordHash']);
        unset($data['invalidateTokenIssuedBefore']);
        $data['username'] = 'changed';

        $this->putTest($data, $data, $data['user']);
    }

    public function test3396PatchAuthenticationWithInvalidation()
    {
        $dataLoader = $this->getDataLoader();
        $allData = $dataLoader->getAll();
        $this->assertArrayHasKey(2, $allData);
        $data = $allData[2];
        $this->assertArrayHasKey('invalidateTokenIssuedBefore', $data);

        unset($data['passwordHash']);
        unset($data['invalidateTokenIssuedBefore']);
        $data['username'] = 'changed';

        $jsonApiData = $dataLoader->createJsonApi($data);
        $this->patchJsonApiTest($data, $jsonApiData);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['user']);
    }

    /**
     * Overridden because authentication uses
     * 'user' the ID
     * @inheritdoc
     */
    protected function getOneTest(): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['user']);
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Overridden because authetication uses
     * 'user' as the ID
     */
    protected function getOneJsonApiTest(): mixed
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOneJsonApi($endpoint, (string) $data['user']);
        $this->assertSame($responseKey = $this->getCamelCasedPluralName(), $returnedData->type);
        $this->compareJsonApiData($data, $returnedData);

        return $returnedData;
    }
    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @dataProvider putsToTest
     * @inheritdoc
     */
    public function testPut($key, $value, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . $this::class . '::putsToTest'
            );
        }
        unset($data['passwordHash']);
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $data['user']);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $i => $data) {
            $data['username'] = 'randomuser' . $i;
            unset($data['passwordHash']);
            $this->putTest($data, $data, $data['user']);
        }
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPatchForAllDataJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $i => $data) {
            $data['username'] = 'randomuser' . $i;
            unset($data['passwordHash']);
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPostMany()
    {
        $data = $this->createMany(10);
        $this->postManyTest($data);
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPostManyJsonApi()
    {
        $arr = $this->createMany(10);
        $data = $this->createBulkJsonApi($arr);
        $this->postManyJsonApiTest($data, $arr);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postTest(array $data, array $postData): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function putTest(array $data, array $postData, $id, $new = false): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $singularResponseKey = $this->getCamelCasedSingularName();
        $responseData = $this->putOne($endpoint, $singularResponseKey, $id, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postManyTest(array $data): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(fn(array $arr) => $arr['user'], $responseData);
        $filters = [
            'filters[user]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Test saving new data to the JSON:API
     */
    protected function postManyJsonApiTest(object $postData, array $data): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[user]' => $ids
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function getOne($endpoint, $responseKey, $userId, $version = null): mixed
    {
        $version = $version ?: $this->apiVersion;
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_authentications_getone",
            ['version' => $version, 'id' => $userId]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * @inheritdoc
     *
     * @dataProvider readOnlyPropertiesToTest
     */
    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for {$key}. " .
                    "Modify " . $this::class . '::readOnlyPropertiesToTest'
                );
            }
            unset($data['passwordHash']);
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id);
        }
    }

    public function anonymousAccessDeniedOneTest()
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_authentications_getone",
                ['version' => $this->apiVersion, 'id' => $data['user']]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }
    public function anonymousAccessDeniedAllTest()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_authentications_getall",
                ['version' => $this->apiVersion]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    protected function anonymousDeniedPutTest(array $data)
    {
        $data['id'] = $data['user'];
        parent::anonymousDeniedPutTest($data);
    }

    protected function anonymousDeniedPatchTest(array $data)
    {
        $data['id'] = $data['user'];
        parent::anonymousDeniedPatchTest($data);
    }
}
