<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * User controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UserControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
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
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'addedViaIlios',
            'examined',
            'alerts',
            'learningMaterials',
        ];
    }

    /**
     * @group controllers_b
     */
    public function testGetUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_users',
                ['id' => $user['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($user),
            json_decode($response->getContent(), true)['users'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllUsers()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.user')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['users']
        );
    }

    /**
     * @group controllers_b
     */
    public function testFindUsers()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'first')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[1]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'second')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'example')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(2, count($gotUsers));
        $this->assertEquals(
            $users[1]['id'],
            $gotUsers[0]['id']
        );
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[1]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'example second')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[2]['id'],
            $gotUsers[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'nobodyxyzmartian')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(0, count($gotUsers));

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => 'newuser')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[1]['id'],
            $gotUsers[0]['id']
        );

        $users = $this->container->get('ilioscore.dataloader.user')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array('q' => '1111@school')),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        $this->assertEquals(1, count($gotUsers));
        $this->assertEquals(
            $users[0]['id'],
            $gotUsers[0]['id']
        );
    }

    /**
     * @group controllers_b
     */
    public function testFindUsersWithRoles()
    {
        $userRole = '1';
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array(
                'q' => 'example',
                'filters[roles][]' => $userRole)),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        foreach ($gotUsers as $gotUser) {
            $this->assertTrue(in_array($userRole, $gotUser['roles']));
        }

        $userRoles = ['1', '4'];
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', array(
                'q' => 'example',
                'filters[roles][]' => $userRole)),
            null,
            $this->getAuthenticatedUserToken()
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('users', $result));
        $gotUsers = $result['users'];
        foreach ($gotUsers as $gotUser) {
            $this->assertNotEmpty(array_intersect($userRoles, $gotUser['roles']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUser()
    {
        $data = $this->container->get('ilioscore.dataloader.user')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['users'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostMultipleUsers()
    {
        $data = [];
        for ($i = 0; $i < 101; $i++) {
            $data[] = $this->container->get('ilioscore.dataloader.user')->create();
        }
        $postData = array_map(function ($arr) {
            unset($arr['id']);
            unset($arr['reminders']);
            unset($arr['learningMaterials']);
            unset($arr['reports']);
            unset($arr['pendingUserUpdates']);
            unset($arr['permissions']);

            return $arr;
        }, $data);
        $firstId = $data[0]['id'];
        for ($i=0; $i < count($data); $i++) {
            $data[$i]['id'] = $firstId + $i;
        }

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['users' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['users'],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostTooManyMultipleUsers()
    {
        $data = [];
        for ($i = 0; $i < 501; $i++) {
            $data[] = $this->container->get('ilioscore.dataloader.user')->create();
        }
        $postData = array_map(function ($arr) {
            unset($arr['id']);
            unset($arr['reminders']);
            unset($arr['learningMaterials']);
            unset($arr['reports']);
            unset($arr['pendingUserUpdates']);
            unset($arr['permissions']);

            return $arr;
        }, $data);
        $firstId = $data[0]['id'];
        for ($i=0; $i < count($data); $i++) {
            $data[$i]['id'] = $firstId + $i;
        }

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['users' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @group controllers_b
     */
    public function testPostUserCourse()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['directedCourses'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_courses',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['courses'][0];
            $this->assertTrue(in_array($newId, $data['directors']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['learnerGroups'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_learnergroups',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['learnerGroups'][0];
            $this->assertTrue(in_array($newId, $data['users']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserInstructorLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['instructedLearnerGroups'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_learnergroups',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['learnerGroups'][0];
            $this->assertTrue(in_array($newId, $data['instructors']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserInstructorGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['instructorGroups'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_instructorgroups',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['instructorGroups'][0];
            $this->assertTrue(in_array($newId, $data['users']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['learnerIlmSessions'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_ilmsessions',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['ilmSessions'][0];
            $this->assertTrue(in_array($newId, $data['learners']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserInstructedIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['instructorIlmSessions'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_ilmsessions',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['ilmSessions'][0];
            $this->assertTrue(in_array($newId, $data['instructors']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['offerings'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_offerings',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['offerings'][0];
            $this->assertTrue(in_array($newId, $data['learners']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserInstructedOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['instructedOfferings'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_offerings',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['offerings'][0];
            $this->assertTrue(in_array($newId, $data['instructors']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['programYears'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_programyears',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['programYears'][0];
            $this->assertTrue(in_array($newId, $data['directors']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostUserCohort()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['cohorts'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_cohorts',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['cohorts'][0];
            $this->assertTrue(in_array($newId, $data['users']));
        }
    }


    /**
     * @group controllers_b
     */
    public function testPostUserithNoIcsFeeDKey()
    {
        $data = $this->container->get('ilioscore.dataloader.user')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);
        unset($postData['icsFeedKey']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $content = json_decode($response->getContent(), true)['users'][0];
        $this->assertEquals(64, strlen($content['icsFeedKey']), var_export($content, true));
    }
    /**
     * @group controllers_b
     */
    public function testPostBadUser()
    {
        $invalidUser = $this->container
            ->get('ilioscore.dataloader.user')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $invalidUser]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutUser1()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne();
        $data['directedCourses'] = ['2'];
        $data['learnerGroups'] = ['2'];
        $data['instructedLearnerGroups'] = ['2'];
        $data['instructorGroups'] = ['2'];
        $data['instructorIlmSessions'] = ['2'];
        $data['learnerIlmSessions'] = ['2'];
        $data['instructedOfferings'] = ['2'];
        $data['programYears'] = ['2'];
        $data['alerts'] = ['2'];
        $data['roles'] = ['2'];
        $data['cohorts'] = ['2'];
        $data['primaryCohort'] = '2';
        $data['directedSchools'] = ['2'];

        $data['userSyncIgnore'] = true;
        $data['firstName'] = 'Omar';
        $data['lastName'] = 'Vizquel';

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);
        unset($postData['alerts']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $data['id']]
            ),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['user']
        );
    }

    /**
     * PUT the second user because it contains many relationships that user 1 does not
     * @group controllers_b
     */
    public function testPutUser2()
    {
        $datas = $this->container
            ->get('ilioscore.dataloader.user')
            ->getAll();
        $data = $datas[1];
        $data['userSyncIgnore'] = true;
        $data['firstName'] = 'Omar';
        $data['lastName'] = 'Vizquel';

        $data['directedCourses'] = ['1'];
        $data['learnerGroups'] = ['4'];
        $data['instructedLearnerGroups'] = ['2'];
        $data['instructorGroups'] = ['4'];
        $data['instructorIlmSessions'] = ['2'];
        $data['learnerIlmSessions'] = ['2'];
        $data['instructedOfferings'] = ['2'];
        $data['programYears'] = ['2'];
        $data['alerts'] = ['2'];
        $data['cohorts'] = ['2'];
        $data['directedSchools'] = ['1'];

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);
        unset($postData['permissions']);
        unset($postData['alerts']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $data['id']]
            ),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['user']
        );
    }

    /**
     * @group controllers_b
     */
    public function testDeleteUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_users',
                ['id' => $user['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_users',
                ['id' => $user['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testUserNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_users', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers_b
     */
    public function testFilterByInstructedCourse()
    {
        $instructors = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[instructedCourses][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByInstructedSession()
    {
        $instructors = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[instructedSessions][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByInstructedSessionType()
    {
        $instructors = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[instructedSessionTypes][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByInstructedLearningMaterial()
    {
        $instructors = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[instructedLearningMaterials]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByInstructorGroup()
    {
        $instructors = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[instructorGroups]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $instructors[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterBySchool()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[schools]' => [1]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $users[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $users[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $users[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByRole()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[roles]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $users[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByCohort()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[cohorts]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $users[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $users[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_b
     */
    public function testFilterByNullCohort()
    {
        $users = $this->container->get('ilioscore.dataloader.user')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_users', ['filters[cohorts]' => 'null']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['users'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $users[2]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $users[3]
            ),
            $data[1]
        );
    }
}
