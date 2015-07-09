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
     * @return array|string
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
            // 'Ilios\CoreBundle\Tests\Fixture\LoadRecurringEventData'
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

    public function testGetOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne()
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
            $this->mockSerialize($offering),
            json_decode($response->getContent(), true)['offerings'][0]
        );
    }

    public function testGetAllOfferings()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_offerings'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.offering')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['offerings']
        );
    }

    public function testPostOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.offering')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            json_encode(['offering' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['offerings'][0],
            $response->getContent()
        );
    }

    public function testPostBadOffering()
    {
        $invalidOffering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            json_encode(['offering' => $invalidOffering])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutOffering()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $data['id']]
            ),
            json_encode(['offering' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['offering']
        );
    }

    public function testDeleteOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne()
        ;

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
            $this->getUrl('get_offerings', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
