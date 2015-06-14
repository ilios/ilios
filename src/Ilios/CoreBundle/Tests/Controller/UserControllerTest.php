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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadApiKeyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserMadeReminderData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructionHoursData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'phone',
            'addedViaIlios',
            'enabled',
            'ucUid',
            'otherId',
            'examined',
            'userSyncIgnore',
            'apiKey',
            'reminders'
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
            )
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
        $this->createJsonRequest('GET', $this->getUrl('cget_users'));
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

    public function testPostUser()
    {
        $data = $this->container->get('ilioscore.dataloader.user')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(['user' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['users'][0]
        );
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
            json_encode(['user' => $invalidUser])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $user['id']]
            ),
            json_encode(['user' => $user])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($user),
            json_decode($response->getContent(), true)['user']
        );
    }

    public function testDeleteUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_users',
                ['id' => $user['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_users',
                ['id' => $user['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUserNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_users', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
