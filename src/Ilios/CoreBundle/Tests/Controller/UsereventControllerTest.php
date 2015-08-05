<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * UserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UsereventControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
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

    public function testGetEvents()
    {
        $user = $this->container->get('ilioscore.dataloader.user')->getOne();
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $ilmSessions = $this->container->get('ilioscore.dataloader.ilmSession')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_userevent',
                ['id' => $user['id'], 'from' => 0, 'to' => 100000000000]
            )
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $events = json_decode($response->getContent(), true)['userEvents'];
        $this->assertEquals(11, count($events), 'Expected events returned');
        $this->assertEquals($events[0]['startDate'], $offerings[1]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[1]['endDate']);
        $this->assertEquals($events[1]['startDate'], $offerings[0]['startDate']);
        $this->assertEquals($events[1]['endDate'], $offerings[0]['endDate']);
        for ($i = 2; $i <=6; $i++) {
            $this->assertEquals($events[$i]['offering'], $i+1);
            $this->assertEquals($events[$i]['startDate'], $offerings[$i]['startDate']);
            $this->assertEquals($events[$i]['endDate'], $offerings[$i]['endDate']);
        }
        for ($i = 7; $i <=10; $i++) {
            $this->assertEquals($events[$i]['ilmSession'], $i-7+1);
            $this->assertEquals($events[$i]['startDate'], $ilmSessions[$i-7]['dueDate']);
        }
    }
}
