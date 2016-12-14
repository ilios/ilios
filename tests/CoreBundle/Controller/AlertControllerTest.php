<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Alert controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AlertControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSchoolData'
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
    public function testGetAlert()
    {
        $alert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alerts',
                ['id' => $alert['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($alert),
            json_decode($response->getContent(), true)['alerts'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAlerts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_alerts'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.alert')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['alerts']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAlert()
    {
        $data = $this->container->get('ilioscore.dataloader.alert')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alerts'),
            json_encode(['alert' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['alerts'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAlert()
    {
        $invalidAlert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_alerts'),
            json_encode(['alert' => $invalidAlert]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutAlert()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.alert')
            ->getOne();
        $data['changeTypes'] = ['2'];
        $data['instigators'] = ['3'];
        $data['recipients'] = ['2'];

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_alerts',
                ['id' => $data['id']]
            ),
            json_encode(['alert' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['alert']
        );
    }

    /**
     * @group controllers_a
     */
    public function testDeleteAlert()
    {
        $alert = $this->container
            ->get('ilioscore.dataloader.alert')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_alerts',
                ['id' => $alert['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_alerts',
                ['id' => $alert['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testAlertNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_alerts', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers_a
     */
    public function testFilterByChangeTypes()
    {
        $alerts = $this->container->get('ilioscore.dataloader.alert')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_alerts', ['filters[changeTypes]' => [1]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['alerts'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByInstigators()
    {
        $alerts = $this->container->get('ilioscore.dataloader.alert')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_alerts', ['filters[instigators]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['alerts'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByRecipients()
    {
        $alerts = $this->container->get('ilioscore.dataloader.alert')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_alerts', ['filters[recipients]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['alerts'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $alerts[2]
            ),
            $data[1]
        );
    }
}
