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
     * @return array
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

    public function testGetSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne()['sessionLearningMaterial']
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
            $sessionLearningMaterial,
            json_decode($response->getContent(), true)['sessionLearningMaterial']
        );
    }

    public function testGetAllSessionLearningMaterials()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessionlearningmaterials'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessionlearningmaterial')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostSessionLearningMaterial()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            json_encode(
                $this->container->get('ilioscore.dataloader.sessionlearningmaterial')
                    ->create()['sessionLearningMaterial']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadSessionLearningMaterial()
    {
        $invalidSessionLearningMaterial = array_shift(
            $this->container->get('ilioscore.dataloader.sessionlearningmaterial')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessionlearningmaterials'),
            $invalidSessionLearningMaterial
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->createWithId()['sessionLearningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            ),
            json_encode($sessionLearningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessionlearningmaterial')
                ->getLastCreated()['sessionLearningMaterial'],
            json_decode($response->getContent(), true)['sessionLearningMaterial']
        );
    }

    public function testDeleteSessionLearningMaterial()
    {
        $sessionLearningMaterial = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->createWithId()['sessionLearningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessionlearningmaterials',
                ['id' => $sessionLearningMaterial['id']]
            ),
            json_encode($sessionLearningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_sessionlearningmaterials', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
