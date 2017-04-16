<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * ProgramYearSteward controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ProgramYearStewardControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearStewardData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDepartmentData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
        ];
    }

    public function testGetProgramYearSteward()
    {
        $programYearSteward = $this->container
            ->get('ilioscore.dataloader.programyearsteward')
            ->getOne()['programYearSteward']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programyearstewards',
                ['id' => $programYearSteward['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $programYearSteward,
            json_decode($response->getContent(), true)['programYearSteward']
        );
    }

    public function testGetAllProgramYearStewards()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_programyearstewards'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.programyearsteward')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostProgramYearSteward()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyearstewards'),
            json_encode(
                $this->container->get('ilioscore.dataloader.programyearsteward')
                    ->create()['programYearSteward']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadProgramYearSteward()
    {
        $invalidProgramYearSteward = array_shift(
            $this->container->get('ilioscore.dataloader.programyearsteward')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyearstewards'),
            $invalidProgramYearSteward
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutProgramYearSteward()
    {
        $programYearSteward = $this->container
            ->get('ilioscore.dataloader.programyearsteward')
            ->createWithId()['programYearSteward']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyearstewards',
                ['id' => $programYearSteward['id']]
            ),
            json_encode($programYearSteward)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.programyearsteward')
                ->getLastCreated()['programYearSteward'],
            json_decode($response->getContent(), true)['programYearSteward']
        );
    }

    public function testDeleteProgramYearSteward()
    {
        $programYearSteward = $this->container
            ->get('ilioscore.dataloader.programyearsteward')
            ->createWithId()['programYearSteward']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyearstewards',
                ['id' => $programYearSteward['id']]
            ),
            json_encode($programYearSteward)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_programyearstewards',
                ['id' => $programYearSteward['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_programyearstewards',
                ['id' => $programYearSteward['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testProgramYearStewardNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_programyearstewards', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
