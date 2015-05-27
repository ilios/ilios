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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'countOfferingsOnce'
        ];
    }

    public function testGetCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
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
            $this->mockSerialize($curriculumInventorySequenceBlockSession),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions'][0]
        );
    }

    public function testGetAllCurriculumInventorySequenceBlockSessions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequenceblocksessions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions']
        );
    }

    public function testPostCurriculumInventorySequenceBlockSession()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(['curriculumInventorySequenceBlockSession' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
        );
    }

    public function testPostBadCurriculumInventorySequenceBlockSession()
    {
        $invalidCurriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(['curriculumInventorySequenceBlockSession' => $invalidCurriculumInventorySequenceBlockSession])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            ),
            json_encode(['curriculumInventorySequenceBlockSession' => $curriculumInventorySequenceBlockSession])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlockSession),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSession']
        );
    }

    public function testDeleteCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
        ;

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
            $this->getUrl('get_curriculuminventorysequenceblocksessions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
