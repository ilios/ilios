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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadIngestionExceptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    public function testGetIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->getOne()['ingestionException']
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
            $ingestionException,
            json_decode($response->getContent(), true)['ingestionException']
        );
    }

    public function testGetAllIngestionExceptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_ingestionexceptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.ingestionexception')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostIngestionException()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ingestionexceptions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.ingestionexception')
                    ->create()['ingestionException']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadIngestionException()
    {
        $invalidIngestionException = array_shift(
            $this->container->get('ilioscore.dataloader.ingestionexception')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ingestionexceptions'),
            $invalidIngestionException
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->createWithId()['ingestionException']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ingestionexceptions',
                ['id' => $ingestionException['user']]
            ),
            json_encode($ingestionException)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.ingestionexception')
                ->getLastCreated()['ingestionException'],
            json_decode($response->getContent(), true)['ingestionException']
        );
    }

    public function testDeleteIngestionException()
    {
        $ingestionException = $this->container
            ->get('ilioscore.dataloader.ingestionexception')
            ->createWithId()['ingestionException']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ingestionexceptions',
                ['id' => $ingestionException['user']]
            ),
            json_encode($ingestionException)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_ingestionexceptions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
