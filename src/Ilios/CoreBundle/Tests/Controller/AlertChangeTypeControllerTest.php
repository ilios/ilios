<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AlertChangeType controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AlertChangeTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertChangeTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'title'
        ];
    }

    public function testGetAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alertchangetypes',
                ['id' => $alertChangeType['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($alertChangeType),
            json_decode($response->getContent(), true)['alertChangeTypes'][0]
        );
    }

    public function testGetAllAlertChangeTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_alertchangetypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.alertchangetype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['alertChangeTypes']
        );
    }

    public function testPostAlertChangeType()
    {
        $data = $this->container->get('ilioscore.dataloader.alertchangetype')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(['alertChangeType' => $data])
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

    public function testPostBadAlertChangeType()
    {
        $invalidAlertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(['alertChangeType' => $invalidAlertChangeType])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alertchangetypes',
                ['id' => $alertChangeType['id']]
            ),
            json_encode(['alertChangeType' => $alertChangeType])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($alertChangeType),
            json_decode($response->getContent(), true)['alertChangeType']
        );
    }

    public function testDeleteAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_alertchangetypes',
                ['id' => $alertChangeType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_alertchangetypes',
                ['id' => $alertChangeType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAlertChangeTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_alertchangetypes', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
