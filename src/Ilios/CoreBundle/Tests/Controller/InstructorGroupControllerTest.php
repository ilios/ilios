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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
        ];
    }

    public function testGetInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne()['instructorGroup']
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
            $instructorGroup,
            json_decode($response->getContent(), true)['instructorGroup']
        );
    }

    public function testGetAllInstructorGroups()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_instructorgroups'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.instructorgroup')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostInstructorGroup()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            json_encode(
                $this->container->get('ilioscore.dataloader.instructorgroup')
                    ->create()['instructorGroup']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadInstructorGroup()
    {
        $invalidInstructorGroup = array_shift(
            $this->container->get('ilioscore.dataloader.instructorgroup')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructorgroups'),
            $invalidInstructorGroup
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->createWithId()['instructorGroup']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructorgroups',
                ['id' => $instructorGroup['id']]
            ),
            json_encode($instructorGroup)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.instructorgroup')
                ->getLastCreated()['instructorGroup'],
            json_decode($response->getContent(), true)['instructorGroup']
        );
    }

    public function testDeleteInstructorGroup()
    {
        $instructorGroup = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->createWithId()['instructorGroup']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructorgroups',
                ['id' => $instructorGroup['id']]
            ),
            json_encode($instructorGroup)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_instructorgroups', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
