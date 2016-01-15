<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
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
     * @group controllers_b
     */
    public function testGetEvents()
    {
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $ilmSessions = $this->container->get('ilioscore.dataloader.ilmSession')->getAll();
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();
        $userId = 2;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_userevent',
                ['id' => $userId, 'from' => 0, 'to' => 100000000000]
            ),
            null,
            $this->getAuthenticatedUserToken()
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
            $courseTitle  = $courses[$sessions[$offerings[$i]['session']-1]['course']-1]['title'];
            $this->assertEquals($events[$i]['courseTitle'], $courseTitle);
        }
        for ($i = 7; $i <=10; $i++) {
            $this->assertEquals($events[$i]['ilmSession'], $i-7+1);
            $this->assertEquals($events[$i]['startDate'], $ilmSessions[$i-7]['dueDate']);
            $courseTitle  = $courses[$sessions[$ilmSessions[$i-7]['session']-1]['course']-1]['title'];
            $this->assertEquals($events[$i]['courseTitle'], $courseTitle);
        }
        foreach ($events as $event) {
            $this->assertEquals($userId, $event['user']);
        }
    }

    /**
     * @group controllers_b
     */
    public function testMultidayEvent()
    {
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $userId = 2;
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_userevent',
                ['id' => $userId, 'from' => $from->getTimestamp(), 'to' => $to->getTimestamp()]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $events = json_decode($response->getContent(), true)['userEvents'];
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }
}
