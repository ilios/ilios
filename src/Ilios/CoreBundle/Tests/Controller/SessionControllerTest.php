<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Session controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionFacetData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDisciplineData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionDescriptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructionHoursData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData'
        ];
    }

    public function testGetSession()
    {
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne()['session']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessions',
                ['id' => $session['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $session,
            json_decode($response->getContent(), true)['session']
        );
    }

    public function testGetAllSessions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_sessions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.session')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostSession()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.session')
                    ->create()['session']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadSession()
    {
        $invalidSession = array_shift(
            $this->container->get('ilioscore.dataloader.session')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessions'),
            $invalidSession
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSession()
    {
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->createWithId()['session']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessions',
                ['id' => $session['id']]
            ),
            json_encode($session)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.session')
                ->getLastCreated()['session'],
            json_decode($response->getContent(), true)['session']
        );
    }

    public function testDeleteSession()
    {
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->createWithId()['session']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessions',
                ['id' => $session['id']]
            ),
            json_encode($session)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_sessions',
                ['id' => $session['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_sessions',
                ['id' => $session['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
