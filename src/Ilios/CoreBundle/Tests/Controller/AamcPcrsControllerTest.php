<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AamcPcrs controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcPcrsControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcPcrsData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData'
        ];
    }

    public function testGetAamcPcrs()
    {
        $aamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->getOne()['aamcPcrs']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $aamcPcrs,
            json_decode($response->getContent(), true)['aamcPcrs']
        );
    }

    public function testGetAllAamcPcrs()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_aamcpcrs'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.aamcpcrs')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostAamcPcrs()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcpcrs'),
            json_encode(
                $this->container->get('ilioscore.dataloader.aamcpcrs')
                    ->create()['aamcPcrs']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadAamcPcrs()
    {
        $invalidAamcPcrs = array_shift(
            $this->container->get('ilioscore.dataloader.aamcpcrs')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcpcrs'),
            $invalidAamcPcrs
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAamcPcrs()
    {
        $aamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->createWithId()['aamcPcrs']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            ),
            json_encode($aamcPcrs)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.aamcpcrs')
                ->getLastCreated()['aamcPcrs'],
            json_decode($response->getContent(), true)['aamcPcrs']
        );
    }

    public function testDeleteAamcPcrs()
    {
        $aamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->createWithId()['aamcPcrs']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            ),
            json_encode($aamcPcrs)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAamcPcrsNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_aamcpcrs', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
