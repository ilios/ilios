<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AamcMethod controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcMethodControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcMethodData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData'
        ];
    }

    public function testGetAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()['aamcMethod']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcmethods',
                ['id' => $aamcMethod['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $aamcMethod,
            json_decode($response->getContent(), true)['aamcMethod']
        );
    }

    public function testGetAllAamcMethods()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_aamcmethods'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.aamcmethod')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostAamcMethod()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(
                $this->container->get('ilioscore.dataloader.aamcmethod')
                    ->create()['aamcMethod']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadAamcMethod()
    {
        $invalidAamcMethod = array_shift(
            $this->container->get('ilioscore.dataloader.aamcmethod')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            $invalidAamcMethod
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->createWithId()['aamcMethod']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            json_encode($aamcMethod)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.aamcmethod')
                ->getLastCreated()['aamcMethod'],
            json_decode($response->getContent(), true)['aamcMethod']
        );
    }

    public function testDeleteAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->createWithId()['aamcMethod']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            json_encode($aamcMethod)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_aamcmethods',
                ['id' => $aamcMethod['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_aamcmethods',
                ['id' => $aamcMethod['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAamcMethodNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_aamcmethods', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
