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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

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
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($instructorGroup),
            json_decode($response->getContent(), true)['instructorGroups'][0]
        );
    }

    public function testGetAllInstructorGroups()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_instructorgroups'));
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
            json_encode(['instructorGroup' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['instructorGroups'][0],
            $response->getContent()
        );
    }

    public function testPostBadInstructorGroup()
    {
        $invalidInstructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(['instructorGroup' => $invalidInstructorGroup])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

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
            json_encode(['instructorGroup' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['instructorGroup']
        );
    }

    public function testDeleteInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_instructorgroups',
                ['id' => $instructorGroup['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_instructorgroups',
                ['id' => $instructorGroup['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testInstructorGroupNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_instructorgroups', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
