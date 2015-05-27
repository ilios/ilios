<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * MeshUserSelection controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshUserSelectionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshUserSelectionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'searchPhrase'
        ];
    }

    public function testGetMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshuserselections',
                ['id' => $meshUserSelection['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshUserSelection),
            json_decode($response->getContent(), true)['meshUserSelections'][0]
        );
    }

    public function testGetAllMeshUserSelections()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshuserselections'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshuserselection')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshUserSelections']
        );
    }

    public function testPostMeshUserSelection()
    {
        $data = $this->container->get('ilioscore.dataloader.meshuserselection')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshuserselections'),
            json_encode(['meshUserSelection' => $data])
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

    public function testPostBadMeshUserSelection()
    {
        $invalidMeshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshuserselections'),
            json_encode(['meshUserSelection' => $invalidMeshUserSelection])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshuserselections',
                ['id' => $meshUserSelection['id']]
            ),
            json_encode(['meshUserSelection' => $meshUserSelection])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshUserSelection),
            json_decode($response->getContent(), true)['meshUserSelection']
        );
    }

    public function testDeleteMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshuserselections',
                ['id' => $meshUserSelection['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshuserselections',
                ['id' => $meshUserSelection['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshUserSelectionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshuserselections', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
