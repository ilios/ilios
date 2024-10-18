<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\UserData;
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

/**
 * Authentication API endpoint Test.
 * @group api_5
 */
class AuthenticationTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'authentications';
    protected bool $isGraphQLTestable = false;
    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

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

    public static function putsToTest(): array
    {
        return [
            'username' => ['username', 'devnull'],
            'password' => ['password', 'geheimsache'],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'invalidateTokenIssuedBefore' => ['invalidateTokenIssuedBefore', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'user' => [[1], ['user' => 2]],
            'username' => [[1], ['username' => 'newuser']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }

    public function testPostMultipleAuthenticationWithEmptyPassword(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $data = $this->createMany(101, $jwt);
        $data = array_map(function ($arr) {
            unset($arr['password']);
            return $arr;
        }, $data);
        $this->postManyTest($data, $jwt);
    }

    public function testPostMultipleAuthenticationWithEmptyPasswordJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $arr = $this->createMany(101, $jwt);
        $arr = array_map(function ($item) {
            unset($item['password']);
            return $item;
        }, $arr);

        $data = $this->createBulkJsonApi($arr);
        $this->postManyJsonApiTest($data, $arr, $jwt);
    }

    public function testPostAuthenticationWithEmptyPassword(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['password']);

        $this->postTest($data, $data, $jwt);
    }

    public function testPostAuthenticationWithEmptyPasswordJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        unset($arr['password']);
        $data = $dataLoader->createJsonApi($arr);

        $this->postJsonApiTest($data, $arr, $jwt);
    }

    public function testPutAuthenticationWithNewUsernameAndPassword(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['passwordHash']);
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';

        $this->putTest($data, $data, $data['user'], $jwt);
    }

    public function testPutAuthenticationWithNewUsernameAndPasswordJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['passwordHash']);
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';
        $jsonApiData = $dataLoader->createJsonApi($data);

        $this->patchJsonApiTest($data, $jsonApiData, $jwt);
    }

    public function testPostAuthenticationForUserWithNonPrimarySchool(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['user'] = '4';
        $user4 = parent::getOne('users', 'users', 4, $jwt);
        $this->assertSame($user4['school'], 2, 'User #4 should be in school 2 or this test is garbage');

        $this->postTest($data, $data, $jwt);
    }

    public function testPostAuthenticationWithNoUsernameOrPassword(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['username']);
        unset($data['password']);

        $this->postTest($data, $data, $jwt);
    }

    public function testPostAuthenticationWithNoUsernameOrPasswordJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        unset($arr['username']);
        unset($arr['password']);
        $data = $dataLoader->createJsonApi($arr);

        $this->postJsonApiTest($data, $arr, $jwt);
    }

    public function test3396PutAuthenticationWithInvalidation(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $allData = $dataLoader->getAll();
        $this->assertArrayHasKey(2, $allData);
        $data = $allData[2];
        $this->assertArrayHasKey('invalidateTokenIssuedBefore', $data);

        unset($data['passwordHash']);
        unset($data['invalidateTokenIssuedBefore']);
        $data['username'] = 'changed';

        $this->putTest($data, $data, $data['user'], $jwt);
    }

    public function test3396PatchAuthenticationWithInvalidation(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $allData = $dataLoader->getAll();
        $this->assertArrayHasKey(2, $allData);
        $data = $allData[2];
        $this->assertArrayHasKey('invalidateTokenIssuedBefore', $data);

        unset($data['passwordHash']);
        unset($data['invalidateTokenIssuedBefore']);
        $data['username'] = 'changed';

        $jsonApiData = $dataLoader->createJsonApi($data);
        $this->patchJsonApiTest($data, $jsonApiData, $jwt);
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
                'app_api_applicationconfigs_delete',
                ['version' => $this->apiVersion, 'id' => $data['user']],
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_post',
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
                'app_api_applicationconfigs_post',
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
                'app_api_applicationconfigs_put',
                ['version' => $this->apiVersion, 'id' => $data['user']],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_patch',
                ['version' => $this->apiVersion, 'id' => $data['user']],
            ),
            json_encode([])
        );
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function runDeleteTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['user'], $jwt);
    }

    /**
     * Overridden because authentication uses
     * 'user' the ID
     */
    protected function getOneTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['user'], $jwt);
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Overridden because authentication uses
     * 'user' as the ID
     */
    protected function getOneJsonApiTest(string $jwt): object
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOneJsonApi($endpoint, (string) $data['user'], $jwt);
        $this->assertSame($this->getCamelCasedPluralName(), $returnedData->type);
        $this->compareJsonApiData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @dataProvider putsToTest
     */
    protected function runPutTest($key, $value, string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for $key. " .
                "Modify " . static::class . '::putsToTest'
            );
        }
        unset($data['passwordHash']);
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $data['user'], $jwt);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function runPutForAllDataTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $i => $data) {
            $data['username'] = 'randomuser' . $i;
            unset($data['passwordHash']);
            $this->putTest($data, $data, $data['user'], $jwt);
        }
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function runPatchForAllDataJsonApiTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $i => $data) {
            $data['username'] = 'randomuser' . $i;
            unset($data['passwordHash']);
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData, $jwt);
        }
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function runPostManyTest(string $jwt): void
    {
        $data = $this->createMany(10, $jwt);
        $this->postManyTest($data, $jwt);
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function runPostManyJsonApiTest(string $jwt): void
    {
        $arr = $this->createMany(10, $jwt);
        $data = $this->createBulkJsonApi($arr);
        $this->postManyJsonApiTest($data, $arr, $jwt);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function postTest(array $data, array $postData, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData, $jwt);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user'], $jwt);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function putTest(array $data, array $postData, mixed $id, string $jwt, $new = false): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $singularResponseKey = $this->getCamelCasedSingularName();
        $responseData = $this->putOne($endpoint, $singularResponseKey, $id, $postData, $jwt);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user'], $jwt);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function postManyTest(array $data, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data, $jwt);
        $ids = array_map(fn(array $arr) => $arr['user'], $responseData);
        $filters = [
            'filters[user]' => $ids,
            'limit' => count($ids),
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    protected function postManyJsonApiTest(object $postData, array $data, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData, $jwt);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[user]' => $ids,
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     */
    protected function getOne(
        string $endpoint,
        string $responseKey,
        mixed $id,
        string $jwt,
        ?string $version = null
    ): array {
        $version = $version ?: $this->apiVersion;
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_authentications_getone",
            ['version' => $version, 'id' => $id]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * Overwritten b/c we need to unset the passwordHash
     */
    protected function runPutReadOnlyTest(
        string $jwt,
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
    ): void {
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for $key. " .
                    "Modify " . static::class . '::readOnlyPropertiesToTest'
                );
            }
            unset($data['passwordHash']);
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id, $jwt);
        }
    }

    protected function anonymousAccessDeniedOneTest(): void
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

    protected function anonymousAccessDeniedAllTest(): void
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

    protected function anonymousDeniedPutTest(array $data): void
    {
        $data['id'] = $data['user'];
        parent::anonymousDeniedPutTest($data);
    }

    protected function anonymousDeniedPatchTest(array $data): void
    {
        $data['id'] = $data['user'];
        parent::anonymousDeniedPatchTest($data);
    }

    /**
     * @param int $count
     * @param string $jwt
     * @return array
     */
    protected function createMany(int $count, string $jwt): array
    {
        $userDataLoader = self::getContainer()->get(UserData::class);
        $users = $userDataLoader->createMany($count);
        $savedUsers = $this->postMany('users', 'users', $users, $jwt);

        $dataLoader = $this->getDataLoader();

        return array_map(function ($user) use ($dataLoader) {
            $arr = $dataLoader->create();
            $arr['user'] = (string) $user['id'];
            $arr['username'] .= $user['id'];

            return $arr;
        }, $savedUsers);
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
                            'type' => 'users',
                        ],
                    ],
                ],
            ];
            if (array_key_exists('password', $user)) {
                $rhett['attributes']['password'] = $user['password'];
            }

            return $rhett;
        }, $arr);

        return json_decode(json_encode(['data' => $data]), false);
    }

    protected function compareData(array $expected, array $result): void
    {
        unset($expected['passwordHash']);
        unset($expected['password']);
        unset($expected['invalidateTokenIssuedBefore']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    protected function compareJsonApiData(array $expected, object $result): void
    {
        $this->assertEquals($expected['user'], $result->id);
        $this->assertEquals($expected['username'], $result->attributes->username);
    }
}
