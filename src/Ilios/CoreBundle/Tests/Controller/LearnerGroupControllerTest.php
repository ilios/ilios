<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * LearnerGroup controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearnerGroupControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    public function testGetLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()['learnerGroup']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learnergroups',
                ['id' => $learnerGroup['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $learnerGroup,
            json_decode($response->getContent(), true)['learnerGroup']
        );
    }

    public function testGetAllLearnerGroups()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learnergroups'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learnergroup')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostLearnerGroup()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(
                $this->container->get('ilioscore.dataloader.learnergroup')
                    ->create()['learnerGroup']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadLearnerGroup()
    {
        $invalidLearnerGroup = array_shift(
            $this->container->get('ilioscore.dataloader.learnergroup')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            $invalidLearnerGroup
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->createWithId()['learnerGroup']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learnergroups',
                ['id' => $learnerGroup['id']]
            ),
            json_encode($learnerGroup)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learnergroup')
                ->getLastCreated()['learnerGroup'],
            json_decode($response->getContent(), true)['learnerGroup']
        );
    }

    public function testDeleteLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->createWithId()['learnerGroup']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learnergroups',
                ['id' => $learnerGroup['id']]
            ),
            json_encode($learnerGroup)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_learnergroups',
                ['id' => $learnerGroup['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_learnergroups',
                ['id' => $learnerGroup['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testLearnerGroupNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learnergroups', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
