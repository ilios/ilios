<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * IngestionException controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class IngestionExceptionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadIngestionExceptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
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

    public function testGetIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_ingestionexceptions',
                ['id' => $ingestionException['user']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($ingestionException),
            json_decode($response->getContent(), true)['ingestionExceptions'][0]
        );
    }

    public function testGetAllIngestionExceptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_ingestionexceptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.ingestionexception')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['ingestionExceptions']
        );
    }

    public function testPostIngestionException()
    {
        $data = $this->container->get('ilioscore.dataloader.ingestionexception')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ingestionexceptions'),
            json_encode(['ingestionException' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['ingestionExceptions'][0]
        );
    }

    public function testPostBadIngestionException()
    {
        $invalidIngestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ingestionexceptions'),
            json_encode(['ingestionException' => $invalidIngestionException])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ingestionexceptions',
                ['id' => $ingestionException['user']]
            ),
            json_encode(['ingestionException' => $ingestionException])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($ingestionException),
            json_decode($response->getContent(), true)['ingestionException']
        );
    }

    public function testDeleteIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_ingestionexceptions',
                ['id' => $ingestionException['user']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_ingestionexceptions',
                ['id' => $ingestionException['user']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testIngestionExceptionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_ingestionexceptions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
