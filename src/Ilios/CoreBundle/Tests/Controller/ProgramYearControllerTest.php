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
     * @return array
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

    public function testGetProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()['programYear']
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
            $programYear,
            json_decode($response->getContent(), true)['programYear']
        );
    }

    public function testGetAllProgramYears()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_programyears'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.programyear')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostProgramYear()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(
                $this->container->get('ilioscore.dataloader.programyear')
                    ->create()['programYear']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadProgramYear()
    {
        $invalidProgramYear = array_shift(
            $this->container->get('ilioscore.dataloader.programyear')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            $invalidProgramYear
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->createWithId()['programYear']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyears',
                ['id' => $programYear['id']]
            ),
            json_encode($programYear)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.programyear')
                ->getLastCreated()['programYear'],
            json_decode($response->getContent(), true)['programYear']
        );
    }

    public function testDeleteProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->createWithId()['programYear']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyears',
                ['id' => $programYear['id']]
            ),
            json_encode($programYear)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_programyears', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
