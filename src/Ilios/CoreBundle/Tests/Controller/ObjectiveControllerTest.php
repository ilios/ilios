<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Objective controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ObjectiveControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
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

    public function testGetObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_objectives',
                ['id' => $objective['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($objective),
            json_decode($response->getContent(), true)['objectives'][0]
        );
    }

    public function testGetAllObjectives()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_objectives'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.objective')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['objectives']
        );
    }

    public function testPostObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.objective')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['objectives'][0]
        );
    }

    public function testPostBadObjective()
    {
        $invalidObjective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $invalidObjective])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_objectives',
                ['id' => $objective['id']]
            ),
            json_encode(['objective' => $objective])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($objective),
            json_decode($response->getContent(), true)['objective']
        );
    }

    public function testDeleteObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_objectives',
                ['id' => $objective['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_objectives',
                ['id' => $objective['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testObjectiveNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_objectives', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
