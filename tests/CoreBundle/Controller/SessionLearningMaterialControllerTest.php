<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * SessionLearningMaterial controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionLearningMaterialControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
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
    public function testGetSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($sessionLearningMaterial),
            json_decode($response->getContent(), true)['sessionLearningMaterials'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllSessionLearningMaterials()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessionlearningmaterials'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.sessionlearningmaterial')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['sessionLearningMaterials']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostSessionLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.sessionlearningmaterial')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            json_encode(['sessionLearningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['sessionLearningMaterials'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostBadSessionLearningMaterial()
    {
        $invalidSessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            json_encode(['sessionLearningMaterial' => $invalidSessionLearningMaterial]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutSessionLearningMaterial()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessionlearningmaterials',
                ['id' => $data['id']]
            ),
            json_encode(['sessionLearningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['sessionLearningMaterial']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
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
    public function testSessionLearningMaterialNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessionlearningmaterials', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
