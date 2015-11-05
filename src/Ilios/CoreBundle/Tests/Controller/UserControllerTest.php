<?php

namespace Ilios\CoreBundle\Tests\Controller;

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
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserMadeReminderData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPendingUserUpdateData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPermissionData',
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
            'userSyncIgnore',
        ];
    }

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
    }

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

    public function testPostUser()
    {
        $data = $this->container->get('ilioscore.dataloader.user')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserCourse()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserInstructorLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserInstructoGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserInstructedIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserInstructedOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPostUserAlert()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['users'][0]['id'];
        foreach ($postData['alerts'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_alerts',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['alerts'][0];
            $this->assertTrue(in_array($newId, $data['instigators']));
        }
    }

    public function testPostUserCohort()
    {
        $data = $this->container->get('ilioscore.dataloader.user')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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

    public function testPutUser()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['reminders']);
        unset($postData['learningMaterials']);
        unset($postData['publishEvents']);
        unset($postData['reports']);
        unset($postData['pendingUserUpdates']);

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
}
