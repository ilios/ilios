<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AamcPcrs controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcPcrsControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcPcrsData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testGetAamcPcrs()
    {
        $aamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($aamcPcrs),
            json_decode($response->getContent(), true)['aamcPcrses'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAamcPcrs()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_aamcpcrs'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.aamcpcrs')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['aamcPcrses']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAamcPcrs()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcpcrs')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcpcrs'),
            json_encode(['aamcPcrses' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['aamcPcrses'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCompetencyAamcPcrs()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcpcrs')->create();
        $postData = $data;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcpcrs'),
            json_encode(['aamcPcrses' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['aamcPcrses'][0]['id'];
        foreach ($postData['competencies'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_competencies',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['competencies'][0];
            $this->assertTrue(in_array($newId, $data['aamcPcrses']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAamcPcrs()
    {
        $invalidAamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcpcrs'),
            json_encode(['aamcPcrses' => $invalidAamcPcrs]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutAamcPcrs()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcpcrs',
                ['id' => $data['id']]
            ),
            json_encode(['aamcPcrses' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['aamcPcrses']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteAamcPcrs()
    {
        $aamcPcrs = $this->container
            ->get('ilioscore.dataloader.aamcpcrs')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcpcrs',
                ['id' => $aamcPcrs['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testAamcPcrsNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_aamcpcrs', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
