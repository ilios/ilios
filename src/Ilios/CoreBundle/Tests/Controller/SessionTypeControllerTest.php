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
     * @return array|string
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

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'sessionTypeCssClass',
            'assessment'
        ];
    }

    public function testGetSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->getOne()
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
            $this->mockSerialize($sessionType),
            json_decode($response->getContent(), true)['sessionTypes'][0]
        );
    }

    public function testGetAllSessionTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessiontypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.sessiontype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['sessionTypes']
        );
    }

    public function testPostSessionType()
    {
        $data = $this->container->get('ilioscore.dataloader.sessiontype')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiontypes'),
            json_encode(['sessionType' => $data])
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

    public function testPostBadSessionType()
    {
        $invalidSessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessiontypes'),
            json_encode(['sessionType' => $invalidSessionType])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiontypes',
                ['id' => $sessionType['id']]
            ),
            json_encode(['sessionType' => $sessionType])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($sessionType),
            json_decode($response->getContent(), true)['sessionType']
        );
    }

    public function testDeleteSessionType()
    {
        $sessionType = $this->container
            ->get('ilioscore.dataloader.sessiontype')
            ->getOne()
        ;

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
            $this->getUrl('get_sessiontypes', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
