<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * SessionDescription controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionDescriptionControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionDescriptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getOne()['sessionDescription']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $sessionDescription,
            json_decode($response->getContent(), true)['sessionDescription']
        );
    }

    public function testGetAllSessionDescriptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessiondescriptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessiondescription')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostSessionDescription()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiondescriptions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.sessiondescription')
                    ->create()['sessionDescription']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadSessionDescription()
    {
        $invalidSessionDescription = array_shift(
            $this->container->get('ilioscore.dataloader.sessiondescription')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiondescriptions'),
            $invalidSessionDescription
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->createWithId()['sessionDescription']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            ),
            json_encode($sessionDescription)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessiondescription')
                ->getLastCreated()['sessionDescription'],
            json_decode($response->getContent(), true)['sessionDescription']
        );
    }

    public function testDeleteSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->createWithId()['sessionDescription']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            ),
            json_encode($sessionDescription)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testSessionDescriptionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessiondescriptions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
