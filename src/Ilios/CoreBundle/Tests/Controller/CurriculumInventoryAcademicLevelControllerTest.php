<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryAcademicLevel controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryAcademicLevelControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData'
        ];
    }

    public function testGetCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->getOne()['curriculumInventoryAcademicLevel']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $curriculumInventoryAcademicLevel,
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevel']
        );
    }

    public function testGetAllCurriculumInventoryAcademicLevels()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryacademiclevels'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryacademiclevel')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventoryAcademicLevel()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryacademiclevels'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
                    ->create()['curriculumInventoryAcademicLevel']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventoryAcademicLevel()
    {
        $invalidCurriculumInventoryAcademicLevel = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventoryacademiclevel')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryacademiclevels'),
            $invalidCurriculumInventoryAcademicLevel
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->createWithId()['curriculumInventoryAcademicLevel']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            ),
            json_encode($curriculumInventoryAcademicLevel)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
                ->getLastCreated()['curriculumInventoryAcademicLevel'],
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevel']
        );
    }

    public function testDeleteCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->createWithId()['curriculumInventoryAcademicLevel']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            ),
            json_encode($curriculumInventoryAcademicLevel)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventoryAcademicLevelNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryacademiclevels', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
