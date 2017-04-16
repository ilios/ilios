<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequenceBlockSession controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceBlockSessionControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()['curriculumInventorySequenceBlockSession']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $curriculumInventorySequenceBlockSession,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSession']
        );
    }

    public function testGetAllCurriculumInventorySequenceBlockSessions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequenceblocksessions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventorySequenceBlockSession()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
                    ->create()['curriculumInventorySequenceBlockSession']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventorySequenceBlockSession()
    {
        $invalidCurriculumInventorySequenceBlockSession = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            $invalidCurriculumInventorySequenceBlockSession
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->createWithId()['curriculumInventorySequenceBlockSession']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            ),
            json_encode($curriculumInventorySequenceBlockSession)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
                ->getLastCreated()['curriculumInventorySequenceBlockSession'],
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSession']
        );
    }

    public function testDeleteCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->createWithId()['curriculumInventorySequenceBlockSession']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            ),
            json_encode($curriculumInventorySequenceBlockSession)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventorySequenceBlockSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequenceblocksessions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
