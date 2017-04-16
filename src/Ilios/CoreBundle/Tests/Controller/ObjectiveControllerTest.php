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
     * @return array
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

    public function testGetObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()['objective']
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
            $objective,
            json_decode($response->getContent(), true)['objective']
        );
    }

    public function testGetAllObjectives()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_objectives'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.objective')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostObjective()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(
                $this->container->get('ilioscore.dataloader.objective')
                    ->create()['objective']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadObjective()
    {
        $invalidObjective = array_shift(
            $this->container->get('ilioscore.dataloader.objective')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            $invalidObjective
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->createWithId()['objective']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_objectives',
                ['id' => $objective['id']]
            ),
            json_encode($objective)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.objective')
                ->getLastCreated()['objective'],
            json_decode($response->getContent(), true)['objective']
        );
    }

    public function testDeleteObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->createWithId()['objective']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_objectives',
                ['id' => $objective['id']]
            ),
            json_encode($objective)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_objectives', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
