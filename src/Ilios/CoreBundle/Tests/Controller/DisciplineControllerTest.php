<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Discipline controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class DisciplineControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadDisciplineData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->getOne()['discipline']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_disciplines',
                ['id' => $discipline['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $discipline,
            json_decode($response->getContent(), true)['discipline']
        );
    }

    public function testGetAllDisciplines()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_disciplines'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.discipline')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostDiscipline()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_disciplines'),
            json_encode(
                $this->container->get('ilioscore.dataloader.discipline')
                    ->create()['discipline']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadDiscipline()
    {
        $invalidDiscipline = array_shift(
            $this->container->get('ilioscore.dataloader.discipline')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_disciplines'),
            $invalidDiscipline
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->createWithId()['discipline']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_disciplines',
                ['id' => $discipline['id']]
            ),
            json_encode($discipline)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.discipline')
                ->getLastCreated()['discipline'],
            json_decode($response->getContent(), true)['discipline']
        );
    }

    public function testDeleteDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->createWithId()['discipline']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_disciplines',
                ['id' => $discipline['id']]
            ),
            json_encode($discipline)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_disciplines',
                ['id' => $discipline['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_disciplines',
                ['id' => $discipline['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDisciplineNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_disciplines', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
