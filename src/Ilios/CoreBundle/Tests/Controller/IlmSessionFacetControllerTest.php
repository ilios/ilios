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
     * @return array|string
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

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->getOne()
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
            $this->mockSerialize($ilmSessionFacet),
            json_decode($response->getContent(), true)['ilmSessionFacets'][0]
        );
    }

    public function testGetAllIlmSessionFacets()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_ilmsessionfacets'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.ilmsessionfacet')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['ilmSessionFacets']
        );
    }

    public function testPostIlmSessionFacet()
    {
        $data = $this->container->get('ilioscore.dataloader.ilmsessionfacet')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessionfacets'),
            json_encode(['ilmSessionFacet' => $data])
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

    public function testPostBadIlmSessionFacet()
    {
        $invalidIlmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessionfacets'),
            json_encode(['ilmSessionFacet' => $invalidIlmSessionFacet])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessionfacets',
                ['id' => $ilmSessionFacet['id']]
            ),
            json_encode(['ilmSessionFacet' => $ilmSessionFacet])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($ilmSessionFacet),
            json_decode($response->getContent(), true)['ilmSessionFacet']
        );
    }

    public function testDeleteIlmSessionFacet()
    {
        $ilmSessionFacet = $this->container
            ->get('ilioscore.dataloader.ilmsessionfacet')
            ->getOne()
        ;

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
            $this->getUrl('get_ilmsessionfacets', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
