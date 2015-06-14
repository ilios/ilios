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
     * @return array|string
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

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->getOne()
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
            $this->mockSerialize($discipline),
            json_decode($response->getContent(), true)['disciplines'][0]
        );
    }

    public function testGetAllDisciplines()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_disciplines'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.discipline')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['disciplines']
        );
    }

    public function testPostDiscipline()
    {
        $data = $this->container->get('ilioscore.dataloader.discipline')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_disciplines'),
            json_encode(['discipline' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['disciplines'][0]
        );
    }

    public function testPostBadDiscipline()
    {
        $invalidDiscipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_disciplines'),
            json_encode(['discipline' => $invalidDiscipline])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_disciplines',
                ['id' => $discipline['id']]
            ),
            json_encode(['discipline' => $discipline])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($discipline),
            json_decode($response->getContent(), true)['discipline']
        );
    }

    public function testDeleteDiscipline()
    {
        $discipline = $this->container
            ->get('ilioscore.dataloader.discipline')
            ->getOne()
        ;

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
            $this->getUrl('get_disciplines', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
