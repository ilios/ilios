<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryAcademicLevel controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryAcademicLevelControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadProgramData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_a
     */
    public function testGetCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryAcademicLevel),
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevels'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCurriculumInventoryAcademicLevels()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventoryacademiclevels'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevels']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCurriculumInventoryAcademicLevel()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequenceBlocks']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryacademiclevels'),
            json_encode(['curriculumInventoryAcademicLevel' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevels'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCurriculumInventoryAcademicLevel()
    {
        $invalidCurriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryacademiclevels'),
            json_encode(['curriculumInventoryAcademicLevel' => $invalidCurriculumInventoryAcademicLevel]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutCurriculumInventoryAcademicLevel()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequenceBlocks']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryacademiclevels',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventoryAcademicLevel' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventoryAcademicLevel']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryAcademicLevel = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryacademiclevel')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryacademiclevels',
                ['id' => $curriculumInventoryAcademicLevel['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testCurriculumInventoryAcademicLevelNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryacademiclevels', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
