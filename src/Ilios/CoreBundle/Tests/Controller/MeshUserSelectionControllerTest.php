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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshUserSelectionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    public function testGetMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->getOne()['meshUserSelection']
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
            $meshUserSelection,
            json_decode($response->getContent(), true)['meshUserSelection']
        );
    }

    public function testGetAllMeshUserSelections()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshuserselections'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshuserselection')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshUserSelection()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshuserselections'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshuserselection')
                    ->create()['meshUserSelection']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshUserSelection()
    {
        $invalidMeshUserSelection = array_shift(
            $this->container->get('ilioscore.dataloader.meshuserselection')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshuserselections'),
            $invalidMeshUserSelection
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->createWithId()['meshUserSelection']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshuserselections',
                ['id' => $meshUserSelection['id']]
            ),
            json_encode($meshUserSelection)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshuserselection')
                ->getLastCreated()['meshUserSelection'],
            json_decode($response->getContent(), true)['meshUserSelection']
        );
    }

    public function testDeleteMeshUserSelection()
    {
        $meshUserSelection = $this->container
            ->get('ilioscore.dataloader.meshuserselection')
            ->createWithId()['meshUserSelection']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshuserselections',
                ['id' => $meshUserSelection['id']]
            ),
            json_encode($meshUserSelection)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_meshuserselections', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
