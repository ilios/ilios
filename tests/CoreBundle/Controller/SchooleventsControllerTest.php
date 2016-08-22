<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * UserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SchooleventsControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
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
     * @group controllers_b
     */
    public function testGetEvents()
    {
        $school = $this->container->get('ilioscore.dataloader.school')->getOne();
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $ilmSessions = $this->container->get('ilioscore.dataloader.ilmSession')->getAll();
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schoolevents',
                ['id' => $school['id'], 'from' => 0, 'to' => 100000000000]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $events = json_decode($response->getContent(), true)['events'];
        $this->assertEquals($events[0]['offering'], 3);
        $this->assertEquals($events[0]['startDate'], $offerings[2]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[2]['endDate']);
        $this->assertEquals($events[0]['courseTitle'], $courses[0]['title']);

        $this->assertEquals($events[1]['offering'], 4);
        $this->assertEquals($events[1]['startDate'], $offerings[3]['startDate']);
        $this->assertEquals($events[1]['endDate'], $offerings[3]['endDate']);
        $this->assertEquals($events[1]['courseTitle'], $courses[0]['title']);

        $this->assertEquals($events[2]['offering'], 5);
        $this->assertEquals($events[2]['startDate'], $offerings[4]['startDate']);
        $this->assertEquals($events[2]['endDate'], $offerings[4]['endDate']);
        $this->assertEquals($events[2]['courseTitle'], $courses[0]['title']);

        $this->assertEquals($events[3]['offering'], 6);
        $this->assertEquals($events[3]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[3]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[3]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[4]['offering'], 7);
        $this->assertEquals($events[4]['startDate'], $offerings[6]['startDate']);
        $this->assertEquals($events[4]['endDate'], $offerings[6]['endDate']);
        $this->assertEquals($events[4]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[5]['ilmSession'], 1);
        $this->assertEquals($events[5]['startDate'], $ilmSessions[0]['dueDate']);
        $this->assertEquals($events[5]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[6]['ilmSession'], 2);
        $this->assertEquals($events[6]['startDate'], $ilmSessions[1]['dueDate']);
        $this->assertEquals($events[6]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[7]['ilmSession'], 3);
        $this->assertEquals($events[7]['startDate'], $ilmSessions[2]['dueDate']);
        $this->assertEquals($events[7]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[8]['ilmSession'], 4);
        $this->assertEquals($events[8]['startDate'], $ilmSessions[3]['dueDate']);
        $this->assertEquals($events[8]['courseTitle'], $courses[1]['title']);

        $this->assertEquals($events[9]['offering'], 2);
        $this->assertEquals($events[9]['startDate'], $offerings[1]['startDate']);
        $this->assertEquals($events[9]['endDate'], $offerings[1]['endDate']);
        $this->assertEquals($events[9]['courseTitle'], $courses[0]['title']);

        $this->assertEquals($events[10]['offering'], 1);
        $this->assertEquals($events[10]['startDate'], $offerings[0]['startDate']);
        $this->assertEquals($events[10]['endDate'], $offerings[0]['endDate']);
        $this->assertEquals($events[10]['courseTitle'], $courses[0]['title']);


        foreach ($events as $event) {
            $this->assertEquals($school['id'], $event['school']);
        }
    }

    /**
     * @group controllers_b
     */
    public function testMultidayEvent()
    {
        $school = $this->container->get('ilioscore.dataloader.school')->getOne();
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schoolevents',
                ['id' => $school['id'], 'from' => $from->getTimestamp(), 'to' => $to->getTimestamp()]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $events = json_decode($response->getContent(), true)['events'];
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }
}
