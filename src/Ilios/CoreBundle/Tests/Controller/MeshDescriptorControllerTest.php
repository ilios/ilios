<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * MeshDescriptor controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshDescriptorControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshQualifierData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshPreviousIndexingData'
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

    public function testGetMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshDescriptor),
            json_decode($response->getContent(), true)['meshDescriptors'][0]
        );
    }

    public function testGetAllMeshDescriptors()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshdescriptors'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshdescriptor')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshDescriptors']
        );
    }

    public function testPostMeshDescriptor()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $data])
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

    public function testPostBadMeshDescriptor()
    {
        $invalidMeshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $invalidMeshDescriptor])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            json_encode(['meshDescriptor' => $meshDescriptor])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshDescriptor),
            json_decode($response->getContent(), true)['meshDescriptor']
        );
    }

    public function testDeleteMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshDescriptorNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshdescriptors', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
