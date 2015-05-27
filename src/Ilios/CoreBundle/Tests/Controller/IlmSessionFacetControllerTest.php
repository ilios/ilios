<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * IlmSessionFacet controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class IlmSessionFacetControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->getOne()['ilmSessionFacet']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $ilmSessionFacet,
            json_decode($response->getContent(), true)['ilmSessionFacet']
        );
    }

    public function testGetAllIlmSessionFacets()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_ilmsessionfacets'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.ilmsessionfacet')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostIlmSessionFacet()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessionfacets'),
            json_encode(
                $this->container->get('ilioscore.dataloader.ilmsessionfacet')
                    ->create()['ilmSessionFacet']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadIlmSessionFacet()
    {
        $invalidIlmSessionFacet = array_shift(
            $this->container->get('ilioscore.dataloader.ilmsessionfacet')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessionfacets'),
            $invalidIlmSessionFacet
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->createWithId()['ilmSessionFacet']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            ),
            json_encode($ilmSessionFacet)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.ilmsessionfacet')
                ->getLastCreated()['ilmSessionFacet'],
            json_decode($response->getContent(), true)['ilmSessionFacet']
        );
    }

    public function testDeleteIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->createWithId()['ilmSessionFacet']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            ),
            json_encode($ilmSessionFacet)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testIlmSessionFacetNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_ilmsessionfacets', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
