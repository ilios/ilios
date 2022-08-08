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
use Symfony\Component\HttpFoundation\Response;
use App\Tests\ReadWriteEndpointTest;

/**
 * User API endpoint Test.
 * @group api_1
 */
class UserTest extends ReadWriteEndpointTest
{
    protected string $testName =  'users';

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

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
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
            'reports' => ['reports', [1], $skipped = true],
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
            'cohorts' => ['cohorts', [2], $skipped = true],
            'primaryCohort' => ['primaryCohort', 3, $skipped = true],
            'pendingUserUpdates' => ['pendingUserUpdates', [2], $skipped = true],
            'directedSchools' => ['directedSchools', [2]],
            'administeredSchools' => ['administeredSchools', [1, 2]],
            'directedPrograms' => ['directedPrograms', [2]],
            'administeredCurriculumInventoryReports' => ['administeredCurriculumInventoryReports', [2]],
            'root' => ['root', true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
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
//            'authentication' => [[2], ['authentication' => 2]],
//            'reports' => [[1], ['reports' => [1]]],
            'school' => [[0, 1, 2, 4], ['school' => 1]],
            'schools' => [[0, 1, 2, 4], ['schools' => [1]]],
//            'directedCourses' => [[0], ['directedCourses' => [1]]],
//            'administeredCourses' => [[0], ['administeredCourses' => [1]]],
//            'administeredSessions' => [[0], ['administeredSessions' => [1]]],
//            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
//            'instructedLearnerGroups' => [[0], ['instructedLearnerGroups' => [1]]],
//            'instructorGroups' => [[1], ['instructorGroups' => [1, 2, 3]]],
//            'instructorIlmSessions' => [[0], ['instructorIlmSessions' => [1]]],
//            'learnerIlmSessions' => [[0], ['learnerIlmSessions' => [1]]],
//            'offerings' => [[0], ['offerings' => [1]]],
//            'instructedOfferings' => [[0], ['instructedOfferings' => [1]]],
//            'programYears' => [[0], ['programYears' => [1]]],
            'roles' => [[2], ['roles' => [2]]],
            'cohorts' => [[0, 1], ['cohorts' => [1]]],
            'nullCohorts' => [[2, 3, 4], ['cohorts' => 'null']],
            'primaryCohort' => [[0], ['primaryCohort' => 1]],
//            'nullPrimaryCohort' => [[1, 2, 3, 4], ['primaryCohort' => null]],
//            'pendingUserUpdates' => [[0], ['pendingUserUpdates' => [1]]],
//            'directedSchools' => [[0], ['directedSchools' => [1]]],
//            'administeredSchools' => [[0], ['administeredSchools' => [1]]],
//            'directedPrograms' => [[0], ['directedPrograms' => [1]]],
            'isRoot' => [[1], ['root' => true]],
            'isNotRoot' => [[0, 2, 3, 4], ['root' => false]],
            'instructedCourses' => [[1, 3], ['instructedCourses' => [1]]],
            'instructedSessions' => [[1, 3], ['instructedSessions' => [2]]],
            'instructedSessionTypes' => [[0, 1, 3], ['instructedSessionTypes' => [2]]],
            'instructedLearningMaterials' => [[0, 1], ['instructedLearningMaterials' => [1, 2, 3]]],
            'learnerSessions' => [[1, 4], ['learnerSessions' => [1]]],
            'administeredCurriculumInventoryReports' => [],
        ];
    }

    public function qsToTest()
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
        $filters = ['q' => $all[0]['firstName'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'school.edu', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffset()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0];
        $this->filterTest($filters, [$all[0]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffsetAndLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'school.edu', 'offset' => 2, 'limit' => 2];
        $this->filterTest($filters, [$all[2], $all[3]]);
    }

    public function testFindByQWithOffsetAndLimitJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['firstName'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'school.edu', 'offset' => 2, 'limit' => 2];
        $this->jsonApiFilterTest($filters, [$all[2], $all[3]]);
    }

    public function findUsersWithRoleOne()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[1];
        $filters = [
            'q' => 'example',
            'filters[userRole]' => [1]
        ];
        $this->filterTest($filters, $expectedData);
    }

    public function findUsersWithRoleOneAndFour()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData[] = $all[0];
        $expectedData[] = $all[1];
        $filters = [
            'q' => 'example',
            'filters[userRole]' => [1, 4]
        ];
        $this->filterTest($filters, $expectedData);
    }

    public function testRejectUnprivilegedPostRootUser()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $userId = $user['id'];

        $postData = $dataLoader->create();
        $postData['root'] = true;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_post',
                ['version' => $this->apiVersion]
            ),
            json_encode(['users' => [$postData]])
        );
    }

    public function testPostRootUserAsRootUser()
    {
        // PLAN OF ACTION
        // 1. POST a root user.
        // 2. Then, use that root user to POST a new root user.
        // 3. Check for success.

        // 1.
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['root'] = true;
        $rootUser = $this->postOne('users', 'user', 'users', $data);
        $this->assertTrue($rootUser['root']);
        $rootUserToken = $this->getTokenForUser($this->kernelBrowser, $rootUser['id']);

        // 2.
        $data = $dataLoader->create();
        $data['root'] = true;


        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_users_post', [
                'version' => $this->apiVersion
            ]),
            json_encode(['users' => [$data]]),
            $rootUserToken
        );

        // 3.
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
    }

    public function testRejectUnprivilegedChangeUserToRoot()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $userId = $user['id'];

        $postData = $user;
        $postData['root'] = true;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $postData['id']]
            ),
            json_encode(['user' => $postData])
        );
    }

    public function testRejectUnprivilegedAddDeveloperRoleToOwnAccount()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertNotContains(1, $user['roles'], 'User #3 should not be a developer or this test is garbage');
        $userId = $user['id'];

        $postData = $user;
        $postData['roles'] = ['1', '2'];

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $userId]
            ),
            json_encode(['user' => $postData])
        );
    }

    public function testRejectUnprivilegedRemoveRootFromUser()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $userId = $user['id'];

        $postData = $all[1];
        $this->assertTrue($postData['root'], 'User #2 is supposed to be root or this test is garbage');

        $postData['root'] = false;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_put',
                ['version' => $this->apiVersion, 'id' => $postData['id']]
            ),
            json_encode(['user' => $postData])
        );
    }

    public function testUpdateRootAttributeAsRootUser()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[2];
        $this->assertFalse($user['root'], 'User #3 is supposed to not be root or this test is garbage');
        $user['root'] = true;

        $this->putTest($user, $user, $user['id']);
    }

    public function testPostUserCourse()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'directors', 'courses', 'directedCourses');
    }

    public function testPostUserLearnerGroup()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'users', 'learnerGroups', 'learnerGroups');
    }

    public function testPostUserInstructorLearnerGroup()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructors', 'learnerGroups', 'instructedLearnerGroups');
    }

    public function testPostUserInstructorGroup()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'users', 'instructorGroups', 'instructorGroups');
    }

    public function testPostUserIlmSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'learners', 'ilmSessions', 'learnerIlmSessions');
    }

    public function testPostUserInstructedIlmSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructors', 'ilmSessions', 'instructorIlmSessions');
    }

    public function testPostUserOffering()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'learners', 'offerings');
    }

    public function testPostUserInstructedOffering()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructors', 'offerings', 'instructedOfferings');
    }

    public function testPostUserProgramYear()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'directors', 'programYears');
    }

    public function testPostUserCohort()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'users', 'cohorts');
    }

    public function testPostUserWithNoIcsFeedKey()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['icsFeedKey']);

        $response = $this->postOne('users', 'user', 'users', $data);
        $this->assertEquals(64, strlen($response['icsFeedKey']), 'Not ICS feed key for user');
    }

    public function testPostUserWithNullIcsFeedKey()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['icsFeedKey'] = null;

        $response = $this->postOne('users', 'user', 'users', $data);
        $this->assertEquals(64, strlen($response['icsFeedKey']), 'Not ICS feed key for user');
    }

    public function testPostUserWithNullAuthentication()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['authentication']);
        $postData = $data;
        $postData['authentication'] = null;

        $this->postTest($data, $postData);
    }

    public function testUpdateOwnIcsFeedKey()
    {
        $dataLoader = $this->getDataLoader();
        $user = $dataLoader->getOne();

        //root users have too much permission
        $this->assertFalse($user['root']);
        $userId = $user['id'];
        $user['icsFeedKey'] = str_repeat('x', 64);

        $this->putOne('users', 'user', $userId, $user, false, $userId);
        //re-fetch the data to test persistence
        $responseData = $this->getOne('users', 'users', $userId);

        $this->compareData($user, $responseData);
    }
}
