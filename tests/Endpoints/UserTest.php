<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAlertData;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadPendingUserUpdateData;
use App\Tests\Fixture\LoadReportData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\Fixture\LoadUserSessionMaterialStatusData;
use App\Tests\QEndpointTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * User API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class UserTest extends AbstractReadWriteEndpoint
{
    use QEndpointTrait;

    protected string $testName =  'users';
    protected bool $enableDeleteTestsWithServiceToken = false;
    protected bool $enablePostTestsWithServiceToken = false;
    protected bool $enablePatchTestsWithServiceToken = false;
    protected bool $enablePutTestsWithServiceToken = false;

    protected function getFixtures(): array
    {
        return [
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
            LoadAuthenticationData::class,
            LoadSessionData::class,
            LoadCurriculumInventoryReportData::class,
            LoadUserSessionMaterialStatusData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'lastName' => ['lastName', 'ipsum'],
            'firstName' => ['firstName', 'lorem'],
            'middleName' => ['middleName', 'h'],
            'displayName' => ['displayName', 'dev null'],
            'phone' => ['phone', '000-000-0000'],
            'email' => ['email', 'dev.null@lorem.ipsum'],
            'preferredEmail' => ['preferredEmail', 'lorem.ipsum@dev.null'],
            'pronouns' => ['pronouns', 'they/them/tay'],
            'emptyPronouns' => ['pronouns', ''],
            'nullPronouns' => ['pronouns', null],
            'enabled' => ['enabled', false],
            'campusId' => ['campusId', '1234567890'],
            'otherId' => ['otherId', '00012234'],
            'userSyncIgnore' => ['userSyncIgnore', true],
            'addedViaIlios' => ['addedViaIlios', true],
            'examined' => ['examined', false],
            'icsFeedKey' => ['icsFeedKey', hash('sha256', 'testValueICS')],
            // 'reports' => ['reports', [1]], // skipped
            'school' => ['school', 3],
            'directedCourses' => ['directedCourses', [2]],
            'administeredCourses' => ['administeredCourses', [1, 2]],
            'administeredSessions' => ['administeredSessions', [2]],
            'studentAdvisedCourses' => ['studentAdvisedCourses', [2]],
            'studentAdvisedSessions' => ['studentAdvisedSessions', [2]],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructedLearnerGroups' => ['instructedLearnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'instructorIlmSessions' => ['instructorIlmSessions', [1]],
            'learnerIlmSessions' => ['learnerIlmSessions', [1]],
            'offerings' => ['offerings', [1]],
            'instructedOfferings' => ['instructedOfferings', [1]],
            'programYears' => ['programYears', [2]],
            'roles' => ['roles', [2]],
            // 'cohorts' => ['cohorts', [2]], // skipped
            // 'primaryCohort' => ['primaryCohort', 3], // skipped
            // 'pendingUserUpdates' => ['pendingUserUpdates', [2]], // skipped
            'directedSchools' => ['directedSchools', [2]],
            'administeredSchools' => ['administeredSchools', [1, 2]],
            'directedPrograms' => ['directedPrograms', [2]],
            'administeredCurriculumInventoryReports' => ['administeredCurriculumInventoryReports', [2]],
            'root' => ['root', true],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'lastName' => [[1], ['lastName' => 'first']],
            'firstName' => [[2], ['firstName' => 'second']],
            'middleName' => [[1], ['middleName' => 'first']],
            'phone' => [[1], ['phone' => '415-555-0123']],
            'email' => [[1], ['email' => 'first@example.com']],
            'preferredEmail' => [[2], ['preferredEmail' => 'zweite@example.de']],
            'enabled' => [[0, 1, 2, 3, 4], ['enabled' => true]],
            'notEnabled' => [[], ['enabled' => false]],
            'campusId' => [[0], ['campusId' => '1111@school.edu']],
            'otherId' => [[2], ['otherId' => '001']],
            'userSyncIgnore' => [[1], ['userSyncIgnore' => true]],
            'notUserSyncIgnore' => [[0, 2, 3, 4], ['userSyncIgnore' => false]],
            'addedViaIlios' => [[1], ['addedViaIlios' => true]],
            'notAddedViaIlios' => [[0, 2, 3, 4], ['addedViaIlios' => false]],
            'examined' => [[0, 1], ['examined' => true]],
            'notExamined' => [[2, 3, 4], ['examined' => false]],
            'icsFeedKey' => [[1], ['icsFeedKey' => hash('sha256', '2')]],
            // 'authentication' => [[2], ['authentication' => 2]], // skipped
            // 'reports' => [[1], ['reports' => [1]]], // skipped
            'school' => [[0, 1, 2, 4], ['school' => 1]],
            'schools' => [[0, 1, 2, 4], ['schools' => [1]]],
            // 'directedCourses' => [[0], ['directedCourses' => [1]]], // skipped
            // 'administeredCourses' => [[0], ['administeredCourses' => [1]]], // skipped
            // 'administeredSessions' => [[0], ['administeredSessions' => [1]]], // skipped
            // 'learnerGroups' => [[0], ['learnerGroups' => [1]]], // skipped
            // 'instructedLearnerGroups' => [[0], ['instructedLearnerGroups' => [1]]], // skipped
            'instructorGroups' => [[1], ['instructorGroups' => [1, 4]]],
            // 'instructorIlmSessions' => [[0], ['instructorIlmSessions' => [1]]], // skipped
            // 'learnerIlmSessions' => [[0], ['learnerIlmSessions' => [1]]], // skipped
            // 'offerings' => [[0], ['offerings' => [1]]], // skipped
            // 'instructedOfferings' => [[0], ['instructedOfferings' => [1]]], // skipped
            // 'programYears' => [[0], ['programYears' => [1]]], // skipped
            'roles' => [[2], ['roles' => [2]]],
            'cohorts' => [[0, 1], ['cohorts' => [1]]],
            'nullCohorts' => [[2, 3, 4], ['cohorts' => 'null']],
            'primaryCohort' => [[0], ['primaryCohort' => 1]],
            //'nullPrimaryCohort' => [[1, 2, 3, 4], ['primaryCohort' => null]], // skipped
            // 'pendingUserUpdates' => [[0], ['pendingUserUpdates' => [1]]], // skipped
            // 'directedSchools' => [[0], ['directedSchools' => [1]]], // skipped
            // 'administeredSchools' => [[0], ['administeredSchools' => [1]]], // skipped
            // 'directedPrograms' => [[0], ['directedPrograms' => [1]]], //skipped
            'isRoot' => [[1], ['root' => true]],
            'isNotRoot' => [[0, 2, 3, 4], ['root' => false]],
            'instructedCourses' => [[1, 3], ['instructedCourses' => [1]]],
            'instructedAcademicYear' => [[1, 3], ['instructedAcademicYears' => 2016]],
            'instructedAcademicYears' => [[1, 3], ['instructedAcademicYears' => [2016]]],
            'instructedSessions' => [[1, 3], ['instructedSessions' => [2]]],
            'instructedSessionTypes' => [[0, 1, 3], ['instructedSessionTypes' => [2]]],
            'instructedLearningMaterials' => [[0, 1], ['instructedLearningMaterials' => [1, 2, 3]]],
            'learnerSessions' => [[1, 4], ['learnerSessions' => [1]]],
            'administeredCurriculumInventoryReports' => [],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

        return $filters;
    }

    public static function qsToTest(): array
    {
        return [
            ['first', [1]],
            ['second', [2]],
            ['example', [1, 2]],
            ['example second', [2]],
            ['nobodyxyzmartian', []],
            ['newuser', [1]],
            ['1111@school', [0]],
            ['', [0, 1, 2, 3, 4]],
            ['disnom', [1]],
        ];
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['firstName'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'school.edu', 'limit' => 2];
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
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0];
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
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'school.edu', 'offset' => 2, 'limit' => 2];
        $this->filterTest($filters, [$all[2], $all[3]], $jwt);
    }

    public function testFindByQWithOffsetAndLimitJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'school.edu', 'offset' => 2, 'limit' => 2];
        $this->jsonApiFilterTest($filters, [$all[2], $all[3]], $jwt);
    }

    public function testRejectUnprivilegedPostRootUser(): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, $user['id']);

        $postData = $dataLoader->create();
        $postData['root'] = true;

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_post',
                ['version' => $this->apiVersion]
            ),
            json_encode(['users' => [$postData]])
        );
    }

    public function testPostRootUserAsRootUser(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        // PLAN OF ACTION
        // 1. POST a root user.
        // 2. Then, use that root user to POST a new root user.
        // 3. Check for success.

        // 1.
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['root'] = true;
        $rootUser = $this->postOne('users', 'user', 'users', $data, $jwt);
        $this->assertTrue($rootUser['root']);
        $rootUserToken = $this->createJwtFromUserId($this->kernelBrowser, $rootUser['id']);

        // 2.
        $data = $dataLoader->create();
        $data['root'] = true;


        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_users_post', [
                'version' => $this->apiVersion,
            ]),
            json_encode(['users' => [$data]]),
            $rootUserToken
        );

        // 3.
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
    }

    public function testRejectUnprivilegedPutUserToRoot(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        // create a new user with admin permissions in school 1, who is not root
        $newUser = $dataLoader->create();
        $newUser['administeredSchools'] = [1];
        $administrator = $this->postOne('users', 'user', 'users', $newUser, $jwt);
        $this->assertFalse($administrator['root'], 'Administrator must not be root or this test is garbage');
        $this->assertContains('1', $administrator['administeredSchools']);

        $all = $dataLoader->getAll();
        $user3 = $all[2];

        // ensure our new user *can* make some changes to user3
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, $administrator['id']);
        $this->putOne('users', 'user', $user3['id'], $user3, $jwt);

        $user3['root'] = true;

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $user3['id']]
            ),
            json_encode(['user' => $user3])
        );
    }

    public function testRejectUnprivilegedPatchUserToRoot(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        // create a new user with admin permissions in school 1, who is not root
        $newUser = $dataLoader->create();
        $newUser['administeredSchools'] = [1];
        $administrator = $this->postOne('users', 'user', 'users', $newUser, $jwt);
        $this->assertFalse($administrator['root'], 'Administrator must not be root or this test is garbage');
        $this->assertContains('1', $administrator['administeredSchools']);

        $all = $dataLoader->getAll();
        $user3 = $all[2];

        // ensure our new user *can* make some changes to user3
        $adminJwt = $this->createJwtFromUserId($this->kernelBrowser, $administrator['id']);
        $this->patchOneJsonApi($dataLoader->createJsonApi($user3), $adminJwt);

        $user3['root'] = true;

        $this->canNotJsonApi(
            $this->kernelBrowser,
            $adminJwt,
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $user3['id']]
            ),
            json_encode($dataLoader->createJsonApi($user3))
        );
    }

    public function testRejectUnprivilegedPutDeveloperRoleToOwnAccount(): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertNotContains(1, $user['roles'], 'User #3 should not be a developer or this test is garbage');
        $userId = $user['id'];
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, $userId);

        $postData = $user;
        $postData['roles'] = ['1', '2'];

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $userId]
            ),
            json_encode(['user' => $postData])
        );
    }

    public function testRejectUnprivilegedPutRemoveRootFromUser(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        // create a new user with admin permissions in school 1, who is not root
        $newUser = $dataLoader->create();
        $newUser['administeredSchools'] = [1];
        $administrator = $this->postOne('users', 'user', 'users', $newUser, $jwt);
        $this->assertFalse($administrator['root'], 'Administrator must not be root or this test is garbage');
        $this->assertContains('1', $administrator['administeredSchools']);

        $all = $dataLoader->getAll();
        $user2 = $all[1];

        // ensure our new user *can* make some changes to user3
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, $administrator['id']);
        $this->putOne('users', 'user', $user2['id'], $user2, $jwt);
        $this->assertTrue($user2['root'], 'User #2 is supposed to be root or this test is garbage');

        $user2['root'] = false;

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $user2['id']]
            ),
            json_encode(['user' => $user2])
        );
    }

    public function testRejectUnprivilegedPatchRemoveRootFromUser(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        // create a new user with admin permissions in school 1, who is not root
        $newUser = $dataLoader->create();
        $newUser['administeredSchools'] = [1];
        $administrator = $this->postOne('users', 'user', 'users', $newUser, $jwt);
        $this->assertFalse($administrator['root'], 'Administrator must not be root or this test is garbage');
        $this->assertContains('1', $administrator['administeredSchools']);

        $all = $dataLoader->getAll();
        $user2 = $all[1];

        // ensure our new user *can* make some changes to user3
        $adminJwt = $this->createJwtFromUserId($this->kernelBrowser, $administrator['id']);
        $this->patchOneJsonApi($dataLoader->createJsonApi($user2), $adminJwt);
        $this->assertTrue($user2['root'], 'User #2 is supposed to be root or this test is garbage');

        $user2['root'] = false;

        $this->canNotJsonApi(
            $this->kernelBrowser,
            $adminJwt,
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $user2['id']]
            ),
            json_encode($dataLoader->createJsonApi($user2))
        );
    }

    public function testPutUpdateRootAttributeAsRootUser(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $user['root'] = true;

        $this->putTest($user, $user, $user['id'], $jwt);
    }

    public function testPostUserCourse(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'directors', 'courses', 'directedCourses');
    }

    public function testPostUserLearnerGroup(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'users', 'learnerGroups', 'learnerGroups');
    }

    public function testPostUserInstructorLearnerGroup(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'instructors', 'learnerGroups', 'instructedLearnerGroups');
    }

    public function testPostUserInstructorGroup(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'users', 'instructorGroups', 'instructorGroups');
    }

    public function testPostUserIlmSession(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'learners', 'ilmSessions', 'learnerIlmSessions');
    }

    public function testPostUserInstructedIlmSession(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'instructors', 'ilmSessions', 'instructorIlmSessions');
    }

    public function testPostUserOffering(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'learners', 'offerings');
    }

    public function testPostUserInstructedOffering(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'instructors', 'offerings', 'instructedOfferings');
    }

    public function testPostUserProgramYear(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'directors', 'programYears');
    }

    public function testPostUserCohort(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, $jwt, 'users', 'cohorts');
    }

    public function testPostUserWithNoIcsFeedKey(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['icsFeedKey']);

        $response = $this->postOne('users', 'user', 'users', $data, $jwt);
        $this->assertEquals(64, strlen($response['icsFeedKey']), 'Not ICS feed key for user');
    }

    public function testPostUserWithNullIcsFeedKey(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['icsFeedKey'] = null;

        $response = $this->postOne('users', 'user', 'users', $data, $jwt);
        $this->assertEquals(64, strlen($response['icsFeedKey']), 'Not ICS feed key for user');
    }

    public function testPostUserWithNullAuthentication(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['authentication']);
        $postData = $data;
        $postData['authentication'] = null;

        $this->postTest($data, $postData, $jwt);
    }

    public function testUpdateOwnIcsFeedKey(): void
    {
        $rootUserJwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $user = $dataLoader->getOne();

        //root users have too much permission
        $this->assertFalse($user['root']);
        $userId = $user['id'];
        $user['icsFeedKey'] = str_repeat('x', 64);

        $jwt = $this->createJwtFromUserId($this->kernelBrowser, $userId);
        $this->putOne('users', 'user', $userId, $user, $jwt);
        //re-fetch the data to test persistence
        $responseData = $this->getOne('users', 'users', $userId, $rootUserJwt);

        $this->compareData($user, $responseData);
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
                'app_api_users_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_post',
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
                'app_api_users_post',
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
                'app_api_users_put',
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
                'app_api_users_patch',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
            json_encode([])
        );
    }
}
