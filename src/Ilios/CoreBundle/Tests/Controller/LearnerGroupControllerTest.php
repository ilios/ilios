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
     * @return array|string
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

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()
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
            $this->mockSerialize($learnerGroup),
            json_decode($response->getContent(), true)['learnerGroups'][0]
        );
    }

    public function testGetAllLearnerGroups()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learnergroups'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learnergroup')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['learnerGroups']
        );
    }

    public function testPostLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.learnergroup')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learnerGroups'][0]
        );
    }

    public function testPostBadLearnerGroup()
    {
        $invalidLearnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $invalidLearnerGroup])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learnergroups',
                ['id' => $learnerGroup['id']]
            ),
            json_encode(['learnerGroup' => $learnerGroup])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learnerGroup),
            json_decode($response->getContent(), true)['learnerGroup']
        );
    }

    public function testDeleteLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()
        ;

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
            $this->getUrl('get_learnergroups', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
