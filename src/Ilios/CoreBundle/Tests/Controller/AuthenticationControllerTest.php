<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Authentication controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AuthenticationControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData',
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
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testPostAuthentication()
    {
        $data = $this->container->get('ilioscore.dataloader.authentication')
            ->create();

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentication' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @group controllers_a
     */
    public function testPostMultipleAuthentication()
    {
        //We have to create a set of users to work with first.
        $userData = [];
        for ($i = 0; $i < 101; $i++) {
            $userData[] = $this->container->get('ilioscore.dataloader.user')->create();
        }
        $userData = array_map(function ($arr) {
            unset($arr['id']);
            unset($arr['reminders']);
            unset($arr['learningMaterials']);
            unset($arr['reports']);
            unset($arr['pendingUserUpdates']);
            unset($arr['permissions']);

            return $arr;
        }, $userData);
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['users' => $userData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $savedUsers = json_decode($response->getContent(), true)['users'];

        $data = array_map(function ($user) {
            $arr = $this->container->get('ilioscore.dataloader.authentication')->create();
            $arr['user'] = $user['id'];
            $arr['username'] .= $user['id'];

            return $arr;
        }, $savedUsers);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentications' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $savedUserIds = array_map(function ($arr) {
            return $arr['user'];
        }, json_decode($response->getContent(), true)['authentications']);

        foreach ($data as $proposedUser) {
            $this->assertTrue(in_array($proposedUser['user'], $savedUserIds));
        }
    }



    /**
     * @group controllers_a
     */
    public function testPostMultipleAuthenticationWithEmptyPassword()
    {
        //We have to create a set of users to work with first.
        $userData = [];
        for ($i = 0; $i < 101; $i++) {
            $userData[] = $this->container->get('ilioscore.dataloader.user')->create();
        }
        $userData = array_map(function ($arr) {
            unset($arr['id']);
            unset($arr['reminders']);
            unset($arr['learningMaterials']);
            unset($arr['reports']);
            unset($arr['pendingUserUpdates']);
            unset($arr['permissions']);

            return $arr;
        }, $userData);
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['users' => $userData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $savedUsers = json_decode($response->getContent(), true)['users'];

        $data = array_map(function ($user) {
            $arr = $this->container->get('ilioscore.dataloader.authentication')->create();
            $arr['user'] = $user['id'];
            $arr['username'] .= $user['id'];
            unset($arr['password']);

            return $arr;
        }, $savedUsers);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentications' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $savedUserIds = array_map(function ($arr) {
            return $arr['user'];
        }, json_decode($response->getContent(), true)['authentications']);

        foreach ($data as $proposedUser) {
            $this->assertTrue(in_array($proposedUser['user'], $savedUserIds));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostAuthenticationWithEmptyPassword()
    {
        $data = $this->container->get('ilioscore.dataloader.authentication')
            ->create();
        $data['password'] = null;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentication' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAuthentication()
    {
        $invalidAuthentication = $this->container
            ->get('ilioscore.dataloader.authentication')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentication' => $invalidAuthentication]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutAuthentication()
    {
        $existing = $this->container
            ->get('ilioscore.dataloader.authentication')
            ->getOne();

        $data = [
            'user' => $existing['user'],
            'username' => 'somethingnew',
            'password' => 'somethingnew'
        ];

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_authentications',
                ['userId' => $data['user']]
            ),
            json_encode(['authentication' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
    }
}
