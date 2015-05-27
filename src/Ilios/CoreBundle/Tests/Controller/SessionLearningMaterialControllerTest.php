<?php

namespace Ilios\CoreBundle\Tests\Controller;

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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'notes'
        ];
    }

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
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($sessionLearningMaterial),
            json_decode($response->getContent(), true)['sessionLearningMaterials'][0]
        );
    }

    public function testGetAllSessionLearningMaterials()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessionlearningmaterials'));
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

    public function testPostSessionLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.sessionlearningmaterial')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            json_encode(['sessionLearningMaterial' => $data])
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

    public function testPostBadSessionLearningMaterial()
    {
        $invalidSessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            json_encode(['sessionLearningMaterial' => $invalidSessionLearningMaterial])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            ),
            json_encode(['sessionLearningMaterial' => $sessionLearningMaterial])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($sessionLearningMaterial),
            json_decode($response->getContent(), true)['sessionLearningMaterial']
        );
    }

    public function testDeleteSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testSessionLearningMaterialNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessionlearningmaterials', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
