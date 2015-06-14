<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * ProgramYear controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ProgramYearControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDisciplineData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData'
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

    public function testGetProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programyears',
                ['id' => $programYear['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($programYear),
            json_decode($response->getContent(), true)['programYears'][0]
        );
    }

    public function testGetAllProgramYears()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_programyears'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.programyear')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['programYears']
        );
    }

    public function testPostProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.programyear')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(['programYear' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['programYears'][0]
        );
    }

    public function testPostBadProgramYear()
    {
        $invalidProgramYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(['programYear' => $invalidProgramYear])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyears',
                ['id' => $programYear['id']]
            ),
            json_encode(['programYear' => $programYear])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($programYear),
            json_decode($response->getContent(), true)['programYear']
        );
    }

    public function testDeleteProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_programyears',
                ['id' => $programYear['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_programyears',
                ['id' => $programYear['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testProgramYearNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_programyears', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
