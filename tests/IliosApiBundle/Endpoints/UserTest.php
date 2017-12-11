<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\PermissionData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * User API endpoint Test.
 * @group api_1
 */
class UserTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'users';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadUserMadeReminderData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadPendingUserUpdateData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadReportData',
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'lastName' => ['lastName', $this->getFaker()->text(30)],
            'firstName' => ['firstName', $this->getFaker()->text(20)],
            'middleName' => ['middleName', $this->getFaker()->text(20)],
            'phone' => ['phone', $this->getFaker()->phoneNumber],
            'email' => ['email', $this->getFaker()->email],
            'enabled' => ['enabled', false],
            'campusId' => ['campusId', $this->getFaker()->text(10)],
            'otherId' => ['otherId', $this->getFaker()->text(10)],
            'userSyncIgnore' => ['userSyncIgnore', true],
            'addedViaIlios' => ['addedViaIlios', true],
            'examined' => ['examined', false],
            'icsFeedKey' => ['icsFeedKey', hash('sha256', 'testValueICS')],
            'reminders' => ['reminders', [1], $skipped = true],
            'reports' => ['reports', [1], $skipped = true],
            'school' => ['school', 3],
            'directedCourses' => ['directedCourses', [2]],
            'administeredCourses' => ['administeredCourses', [1, 2]],
            'administeredSessions' => ['administeredSessions', [2]],
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
            'permissions' => ['permissions', [1], $skipped = true],
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
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'lastName' => [[1], ['lastName' => 'first']],
            'firstName' => [[2], ['firstName' => 'second']],
            'middleName' => [[1], ['middleName' => 'first']],
            'phone' => [[1], ['phone' => '415-555-0123']],
            'email' => [[1], ['email' => 'first@example.com']],
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
//            'reminders' => [[1], ['reminders' => [2]]],
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
//            'permissions' => [[0], ['permissions' => [1]]],
//            'directedSchools' => [[0], ['directedSchools' => [1]]],
//            'administeredSchools' => [[0], ['administeredSchools' => [1]]],
//            'directedPrograms' => [[0], ['directedPrograms' => [1]]],
            'isRoot' => [[1], ['root' => true]],
            'isNotRoot' => [[0, 2, 3, 4], ['root' => false]],
            'instructedCourses' => [[1], ['instructedCourses' => [1]]],
            'instructedSessions' => [[1], ['instructedSessions' => [2]]],
            'instructedSessionTypes' => [[0, 1], ['instructedSessionTypes' => [2]]],
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
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData);
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
            $userId,
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'users']),
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
        $rootUserToken = $this->getTokenForUser($rootUser['id']);

        // 2.
        $data = $dataLoader->create();
        $data['root'] = true;


        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', [
                'version' => 'v1',
                'object' => 'users'
            ]),
            json_encode(['users' => [$data]]),
            $rootUserToken
        );

        // 3.
        $response = $this->client->getResponse();
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
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'users', 'id' => $postData['id']]),
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
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'users', 'id' => $userId]),
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
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'users', 'id' => $postData['id']]),
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

    public function testPostUserInDifferentPrimarySchool()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[0];
        $this->assertEquals($user['school'], 1, 'User #1 should be in school 1 or this test is garbage');
        $this->assertFalse($user['root'], 'User #1 should not be root or this test is garbage');
        $this->assertContains(
            1,
            $user['roles'],
            'User #1 should be a developer or this test is garbage'
        );

        $newUserSchool = 2;

        $permissionDataLoader = $this->container->get(PermissionData::class);
        $permission = $permissionDataLoader->create();
        $permission['user'] = $user['id'];
        $permission['canRead'] = true;
        $permission['canWrite'] = true;
        $permission['tableRowId'] = $newUserSchool;
        $permission['tableName'] = 'school';
        $this->postOne('permissions', 'permission', 'permissions', $permission);

        $data = $dataLoader->create();
        $data['school'] = $newUserSchool;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'users']),
            json_encode(['user' => $data]),
            $this->getTokenForUser($user['id'])
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        $responseData = json_decode($response->getContent(), true)['users'][0];

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne('users', 'users', $responseData['id']);

        $this->compareData($data, $fetchedResponseData);
    }

    public function testAddRoleInDifferentPrimarySchool()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $user = $all[0];
        $this->assertEquals($user['school'], 1, 'User #1 should be in school 1 or this test is garbage');
        $this->assertFalse($user['root'], 'User #1 should not be root or this test is garbage');
        $this->assertContains(
            1,
            $user['roles'],
            'User #1 should be a developer or this test is garbage'
        );
        $permissionDataLoader = $this->container->get(PermissionData::class);
        $permission = $permissionDataLoader->create();
        $permission['user'] = $user['id'];
        $permission['canRead'] = true;
        $permission['canWrite'] = true;
        $permission['tableRowId'] = 2;
        $permission['tableName'] = 'school';
        $this->postOne('permissions', 'permission', 'permissions', $permission);

        $data = $all[3];
        $this->assertNotContains(
            1,
            $data['roles'],
            'User #4 should Not be a developer or this test is garbage'
        );
        $this->assertNotEquals(
            1,
            $data['school'],
            'User #4 should Not be in school 1 or this test is garbage'
        );

        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'users', 'id' => $data['id']]),
            json_encode(['user' => $data]),
            $this->getTokenForUser($user['id'])
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $responseData = json_decode($response->getContent(), true)['user'];

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne('users', 'users', $responseData['id']);

        $this->compareData($data, $fetchedResponseData);
    }
}
