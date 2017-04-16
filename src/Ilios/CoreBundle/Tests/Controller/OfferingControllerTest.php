<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Offering controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class OfferingControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadRecurringEventData'
        ];
    }

    public function testGetOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne()['offering']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_offerings',
                ['id' => $offering['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $offering,
            json_decode($response->getContent(), true)['offering']
        );
    }

    public function testGetAllOfferings()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_offerings'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.offering')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostOffering()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            json_encode(
                $this->container->get('ilioscore.dataloader.offering')
                    ->create()['offering']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadOffering()
    {
        $invalidOffering = array_shift(
            $this->container->get('ilioscore.dataloader.offering')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            $invalidOffering
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->createWithId()['offering']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $offering['id']]
            ),
            json_encode($offering)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.offering')
                ->getLastCreated()['offering'],
            json_decode($response->getContent(), true)['offering']
        );
    }

    public function testDeleteOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->createWithId()['offering']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $offering['id']]
            ),
            json_encode($offering)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_offerings',
                ['id' => $offering['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_offerings',
                ['id' => $offering['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testOfferingNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_offerings', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
