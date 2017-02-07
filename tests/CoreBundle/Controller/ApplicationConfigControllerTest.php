<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Application config controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ApplicationConfigControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadApplicationConfigData',
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
    public function testGetApplicationConfig()
    {
        $appConfig = $this->container->get('ilioscore.dataloader.applicationconfig')->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_applicationconfigs',
                ['id' => $appConfig['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($appConfig),
            json_decode($response->getContent(), true)['applicationConfigs'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllVocabularies()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_applicationconfigs'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.applicationconfig')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['applicationConfigs']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostApplicationConfig()
    {
        $data = $this->container->get('ilioscore.dataloader.applicationconfig')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);


        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_applicationconfigs'),
            json_encode(['applicationConfig' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['applicationConfigs'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadApplicationConfig()
    {
        $invalidApplicationConfig = $this->container
            ->get('ilioscore.dataloader.applicationconfig')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_applicationconfigs'),
            json_encode(['applicationConfig' => $invalidApplicationConfig]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutApplicationConfig()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.applicationconfig')
            ->getOne();

        $postData = $data;

        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_applicationconfigs',
                ['id' => $data['id']]
            ),
            json_encode(['applicationConfig' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['applicationConfig']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteApplicationConfig()
    {
        $applicationConfig = $this->container
            ->get('ilioscore.dataloader.applicationconfig')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_applicationconfigs',
                ['id' => $applicationConfig['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_applicationconfigs',
                ['id' => $applicationConfig['id']]
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
    public function testApplicationConfigNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_applicationconfigs', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
