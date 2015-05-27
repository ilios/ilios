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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertChangeTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData'
        ];
    }

    public function testGetAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->getOne()['alertChangeType']
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
            $alertChangeType,
            json_decode($response->getContent(), true)['alertChangeType']
        );
    }

    public function testGetAllAlertChangeTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_alertchangetypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.alertchangetype')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostAlertChangeType()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            json_encode(
                $this->container->get('ilioscore.dataloader.alertchangetype')
                    ->create()['alertChangeType']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadAlertChangeType()
    {
        $invalidAlertChangeType = array_shift(
            $this->container->get('ilioscore.dataloader.alertchangetype')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alertchangetypes'),
            $invalidAlertChangeType
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->createWithId()['alertChangeType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alertchangetypes',
                ['id' => $alertChangeType['id']]
            ),
            json_encode($alertChangeType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.alertchangetype')
                ->getLastCreated()['alertChangeType'],
            json_decode($response->getContent(), true)['alertChangeType']
        );
    }

    public function testDeleteAlertChangeType()
    {
        $alertChangeType = $this->container
            ->get('ilioscore.dataloader.alertchangetype')
            ->createWithId()['alertChangeType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alertchangetypes',
                ['id' => $alertChangeType['id']]
            ),
            json_encode($alertChangeType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_alertchangetypes', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
