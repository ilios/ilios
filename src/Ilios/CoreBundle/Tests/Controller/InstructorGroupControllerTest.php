<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * InstructorGroup controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class InstructorGroupControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
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
     * @group controllers
     */
    public function testGetInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_instructorgroups',
                ['id' => $instructorGroup['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($instructorGroup),
            json_decode($response->getContent(), true)['instructorGroups'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllInstructorGroups()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_instructorgroups'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.instructorgroup')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['instructorGroups']
        );
    }

    /**
     * @group controllers
     */
    public function testPostInstructorGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.instructorgroup')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(['instructorGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['instructorGroups'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostInstructorGroupLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.instructorgroup')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(['instructorGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['instructorGroups'][0]['id'];
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
            $this->assertTrue(in_array($newId, $data['instructorGroups']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostInstructorGroupIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.instructorgroup')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(['instructorGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['instructorGroups'][0]['id'];
        foreach ($postData['ilmSessions'] as $id) {
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
            $this->assertTrue(in_array($newId, $data['instructorGroups']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostBadInstructorGroup()
    {
        $invalidInstructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(['instructorGroup' => $invalidInstructorGroup]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutInstructorGroup()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructorgroups',
                ['id' => $data['id']]
            ),
            json_encode(['instructorGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['instructorGroup']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_instructorgroups',
                ['id' => $instructorGroup['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_instructorgroups',
                ['id' => $instructorGroup['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testInstructorGroupNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_instructorgroups', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
