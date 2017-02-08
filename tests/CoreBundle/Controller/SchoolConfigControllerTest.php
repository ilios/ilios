<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * School config controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SchoolConfigControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadSchoolConfigData',
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
    public function testGetSchoolConfig()
    {
        $appConfig = $this->container->get('ilioscore.dataloader.schoolconfig')->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schoolconfigs',
                ['id' => $appConfig['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($appConfig),
            json_decode($response->getContent(), true)['schoolConfigs'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllVocabularies()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_schoolconfigs'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.schoolconfig')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['schoolConfigs']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostSchoolConfig()
    {
        $data = $this->container->get('ilioscore.dataloader.schoolconfig')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);


        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schoolconfigs'),
            json_encode(['schoolConfig' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['schoolConfigs'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadSchoolConfig()
    {
        $invalidSchoolConfig = $this->container
            ->get('ilioscore.dataloader.schoolconfig')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schoolconfigs'),
            json_encode(['schoolConfig' => $invalidSchoolConfig]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutSchoolConfig()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.schoolconfig')
            ->getOne();

        $postData = $data;

        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_schoolconfigs',
                ['id' => $data['id']]
            ),
            json_encode(['schoolConfig' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['schoolConfig']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteSchoolConfig()
    {
        $schoolConfig = $this->container
            ->get('ilioscore.dataloader.schoolconfig')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_schoolconfigs',
                ['id' => $schoolConfig['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schoolconfigs',
                ['id' => $schoolConfig['id']]
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
    public function testSchoolConfigNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_schoolconfigs', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
