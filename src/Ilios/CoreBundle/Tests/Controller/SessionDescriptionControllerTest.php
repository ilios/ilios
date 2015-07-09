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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionDescriptionData',
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

    public function testGetSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getOne()
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
            $this->mockSerialize($sessionDescription),
            json_decode($response->getContent(), true)['sessionDescriptions'][0]
        );
    }

    public function testGetAllSessionDescriptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessiondescriptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.sessiondescription')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['sessionDescriptions']
        );
    }

    public function testPostSessionDescription()
    {
        $data = $this->container->get('ilioscore.dataloader.sessiondescription')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiondescriptions'),
            json_encode(['sessionDescription' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['sessionDescriptions'][0],
            $response->getContent()
        );
    }

    public function testPostBadSessionDescription()
    {
        $invalidSessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiondescriptions'),
            json_encode(['sessionDescription' => $invalidSessionDescription])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutSessionDescription()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiondescriptions',
                ['id' => $data['id']]
            ),
            json_encode(['sessionDescription' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['sessionDescription']
        );
    }

    public function testDeleteSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getOne()
        ;

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
            $this->getUrl('get_sessiondescriptions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
