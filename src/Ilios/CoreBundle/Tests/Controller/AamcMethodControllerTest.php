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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcMethodData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData'
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

    public function testGetAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()
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
            $this->mockSerialize($aamcMethod),
            json_decode($response->getContent(), true)['aamcMethods'][0]
        );
    }

    public function testGetAllAamcMethods()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_aamcmethods'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.aamcmethod')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['aamcMethods']
        );
    }

    public function testPostAamcMethod()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcmethod')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(['aamcMethod' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['aamcMethods'][0]
        );
    }

    public function testPostBadAamcMethod()
    {
        $invalidAamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(['aamcMethod' => $invalidAamcMethod])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            json_encode(['aamcMethod' => $aamcMethod])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($aamcMethod),
            json_decode($response->getContent(), true)['aamcMethod']
        );
    }

    public function testDeleteAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()
        ;

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
            $this->getUrl('get_aamcmethods', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
