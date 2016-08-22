<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionDescriptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_b
     */
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
                ['id' => $sessionDescription['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($sessionDescription),
            json_decode($response->getContent(), true)['sessionDescriptions'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllSessionDescriptions()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessiondescriptions'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true)['sessionDescriptions'];

        $allSessionDescriptions = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getAll();

        // @todo replace this with a better filtering mechanism based on deletion of parent entities. [ST 2015/11/02]
        $filteredDescriptions = array_filter($allSessionDescriptions, function (array $description) use ($data) {
            $descriptionId = $description['id'];
            $found = array_reduce($data, function ($found, array $description) use ($descriptionId) {
                return $found || $description['id'] === $descriptionId;
            }, false);
            return $found;
        });

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals($this->mockSerialize($filteredDescriptions), $data);
    }

    /**
     * @group controllers_b
     */
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
            json_encode(['sessionDescription' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['sessionDescriptions'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostBadSessionDescription()
    {
        $invalidSessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiondescriptions'),
            json_encode(['sessionDescription' => $invalidSessionDescription]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
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
            json_encode(['sessionDescription' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['sessionDescription']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteSessionDescription()
    {
        $sessionDescription = $this->container
            ->get('ilioscore.dataloader.sessiondescription')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessiondescriptions',
                ['id' => $sessionDescription['session']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testSessionDescriptionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessiondescriptions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
