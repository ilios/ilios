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
     * @return array
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

    public function testGetUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->getOne()['user']
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
            $user,
            json_decode($response->getContent(), true)['user']
        );
    }

    public function testGetAllUsers()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_users'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.user')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostUser()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            json_encode(
                $this->container->get('ilioscore.dataloader.user')
                    ->create()['user']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadUser()
    {
        $invalidUser = array_shift(
            $this->container->get('ilioscore.dataloader.user')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_users'),
            $invalidUser
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->createWithId()['user']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $user['id']]
            ),
            json_encode($user)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.user')
                ->getLastCreated()['user'],
            json_decode($response->getContent(), true)['user']
        );
    }

    public function testDeleteUser()
    {
        $user = $this->container
            ->get('ilioscore.dataloader.user')
            ->createWithId()['user']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_users',
                ['id' => $user['id']]
            ),
            json_encode($user)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_users', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
