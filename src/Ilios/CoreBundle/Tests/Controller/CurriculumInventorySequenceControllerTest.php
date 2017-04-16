<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequence controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData'
        ];
    }

    public function testGetCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne()['curriculumInventorySequence']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $curriculumInventorySequence,
            json_decode($response->getContent(), true)['curriculumInventorySequence']
        );
    }

    public function testGetAllCurriculumInventorySequences()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequences'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequence')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventorySequence()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventorysequence')
                    ->create()['curriculumInventorySequence']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventorySequence()
    {
        $invalidCurriculumInventorySequence = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequence')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            $invalidCurriculumInventorySequence
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->createWithId()['curriculumInventorySequence']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            ),
            json_encode($curriculumInventorySequence)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequence')
                ->getLastCreated()['curriculumInventorySequence'],
            json_decode($response->getContent(), true)['curriculumInventorySequence']
        );
    }

    public function testDeleteCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->createWithId()['curriculumInventorySequence']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            ),
            json_encode($curriculumInventorySequence)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventorySequenceNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequences', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
