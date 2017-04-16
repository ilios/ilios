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
     * @return array
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

    public function testGetMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()['meshDescriptor']
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
            $meshDescriptor,
            json_decode($response->getContent(), true)['meshDescriptor']
        );
    }

    public function testGetAllMeshDescriptors()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshdescriptors'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshDescriptor()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshdescriptor')
                    ->create()['meshDescriptor']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshDescriptor()
    {
        $invalidMeshDescriptor = array_shift(
            $this->container->get('ilioscore.dataloader.meshdescriptor')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            $invalidMeshDescriptor
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->createWithId()['meshDescriptor']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            json_encode($meshDescriptor)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshdescriptor')
                ->getLastCreated()['meshDescriptor'],
            json_decode($response->getContent(), true)['meshDescriptor']
        );
    }

    public function testDeleteMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->createWithId()['meshDescriptor']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            json_encode($meshDescriptor)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_meshdescriptors', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
