<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * SessionType controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAssessmentOptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcMethodData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->getOne()['sessionType']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessiontypes',
                ['id' => $sessionType['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $sessionType,
            json_decode($response->getContent(), true)['sessionType']
        );
    }

    public function testGetAllSessionTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessiontypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessiontype')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostSessionType()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiontypes'),
            json_encode(
                $this->container->get('ilioscore.dataloader.sessiontype')
                    ->create()['sessionType']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadSessionType()
    {
        $invalidSessionType = array_shift(
            $this->container->get('ilioscore.dataloader.sessiontype')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiontypes'),
            $invalidSessionType
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->createWithId()['sessionType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiontypes',
                ['id' => $sessionType['id']]
            ),
            json_encode($sessionType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.sessiontype')
                ->getLastCreated()['sessionType'],
            json_decode($response->getContent(), true)['sessionType']
        );
    }

    public function testDeleteSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->createWithId()['sessionType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiontypes',
                ['id' => $sessionType['id']]
            ),
            json_encode($sessionType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_sessiontypes',
                ['id' => $sessionType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_sessiontypes',
                ['id' => $sessionType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testSessionTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessiontypes', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
