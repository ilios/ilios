<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\OfferingInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\CourseData;
use Tests\CoreBundle\DataLoader\IlmSessionData;
use Tests\CoreBundle\DataLoader\OfferingData;
use Tests\CoreBundle\DataLoader\SchoolData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use DateTime;

/**
 * SchooleventsTest API endpoint Test.
 * @group api_2
 */
class SchooleventsTest extends AbstractEndpointTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadSchoolData'
        ];
    }

    public function testGetEvents()
    {
        $school = $this->container->get(SchoolData::class)->getOne();
        $offerings = $this->container->get(OfferingData::class)->getAll();
        $ilmSessions = $this->container->get(IlmSessionData::class)->getAll();
        $courses = $this->container->get(CourseData::class)->getAll();

        $events = $this->getEvents($school['id'], 0, 100000000000);

        $this->assertEquals($events[0]['offering'], 3);
        $this->assertEquals($events[0]['startDate'], $offerings[2]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[2]['endDate']);
        $this->assertEquals($events[0]['courseTitle'], $courses[0]['title']);
        $this->assertTrue($events[0]['attireRequired'], 'attireRequired is correct for event 0');
        $this->assertTrue($events[0]['equipmentRequired'], 'equipmentRequired is correct for event 0');
        $this->assertTrue($events[0]['supplemental'], 'supplemental is correct for event 0');
        $this->assertTrue($events[0]['attendanceRequired'], 'attendanceRequired is correct for event 0');

        $this->assertEquals($events[1]['offering'], 4);
        $this->assertEquals($events[1]['startDate'], $offerings[3]['startDate']);
        $this->assertEquals($events[1]['endDate'], $offerings[3]['endDate']);
        $this->assertEquals($events[1]['courseTitle'], $courses[0]['title']);
        $this->assertTrue($events[1]['attireRequired'], 'attireRequired is correct for event 1');
        $this->assertTrue($events[1]['equipmentRequired'], 'equipmentRequired is correct for event 1');
        $this->assertTrue($events[1]['supplemental'], 'supplemental is correct for event 1');
        $this->assertTrue($events[1]['attendanceRequired'], 'attendanceRequired is correct for event 1');

        $this->assertEquals($events[2]['offering'], 5);
        $this->assertEquals($events[2]['startDate'], $offerings[4]['startDate']);
        $this->assertEquals($events[2]['endDate'], $offerings[4]['endDate']);
        $this->assertEquals($events[2]['courseTitle'], $courses[0]['title']);
        $this->assertTrue($events[2]['attireRequired'], 'attireRequired is correct for event 2');
        $this->assertTrue($events[2]['equipmentRequired'], 'equipmentRequired is correct for event 2');
        $this->assertTrue($events[2]['supplemental'], 'supplemental is correct for event 2');
        $this->assertTrue($events[2]['attendanceRequired'], 'attendanceRequired is correct for event 2');

        $this->assertEquals($events[3]['offering'], 6);
        $this->assertEquals($events[3]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[3]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[3]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[3]['attireRequired'], 'attireRequired is correct for event 3');
        $this->assertFalse($events[3]['equipmentRequired'], 'equipmentRequired is correct for event 3');
        $this->assertTrue($events[3]['supplemental'], 'supplemental is correct for event 3');
        $this->assertArrayNotHasKey('attendanceRequired', $events[3], 'attendanceRequired is correct for event 3');

        $this->assertEquals($events[4]['offering'], 7);
        $this->assertEquals($events[4]['startDate'], $offerings[6]['startDate']);
        $this->assertEquals($events[4]['endDate'], $offerings[6]['endDate']);
        $this->assertEquals($events[4]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[4]['attireRequired'], 'attireRequired is correct for event 4');
        $this->assertFalse($events[4]['equipmentRequired'], 'equipmentRequired is correct for event 4');
        $this->assertTrue($events[4]['supplemental'], 'supplemental is correct for event 4');
        $this->assertArrayNotHasKey('attendanceRequired', $events[4], 'attendanceRequired is correct for event 4');

        $this->assertEquals($events[5]['ilmSession'], 1);
        $this->assertEquals($events[5]['startDate'], $ilmSessions[0]['dueDate']);
        $this->assertEquals($events[5]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[5]['attireRequired'], 'attireRequired is correct for event 5');
        $this->assertFalse($events[5]['equipmentRequired'], 'equipmentRequired is correct for event 5');
        $this->assertFalse($events[5]['supplemental'], 'supplemental is correct for event 5');
        $this->assertArrayNotHasKey('attendanceRequired', $events[5], 'attendanceRequired is correct for event 5');

        $this->assertEquals($events[6]['ilmSession'], 2);
        $this->assertEquals($events[6]['startDate'], $ilmSessions[1]['dueDate']);
        $this->assertEquals($events[6]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[6]['attireRequired'], 'attireRequired is correct for event 6');
        $this->assertFalse($events[6]['equipmentRequired'], 'equipmentRequired is correct for event 6');
        $this->assertFalse($events[6]['supplemental'], 'supplemental is correct for event 6');
        $this->assertArrayNotHasKey('attendanceRequired', $events[6], 'attendanceRequired is correct for event 6');

        $this->assertEquals($events[7]['ilmSession'], 3);
        $this->assertEquals($events[7]['startDate'], $ilmSessions[2]['dueDate']);
        $this->assertEquals($events[7]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[7]['attireRequired'], 'attireRequired is correct for event 7');
        $this->assertFalse($events[7]['equipmentRequired'], 'equipmentRequired is correct for event 7');
        $this->assertFalse($events[7]['supplemental'], 'supplemental is correct for event 7');
        $this->assertArrayNotHasKey('attendanceRequired', $events[7], 'attendanceRequired is correct for event 7');

        $this->assertEquals($events[8]['ilmSession'], 4);
        $this->assertEquals($events[8]['startDate'], $ilmSessions[3]['dueDate']);
        $this->assertEquals($events[8]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[8]['attireRequired'], 'attireRequired is correct for event 8');
        $this->assertFalse($events[8]['equipmentRequired'], 'equipmentRequired is correct for event 8');
        $this->assertFalse($events[8]['supplemental'], 'supplemental is correct for event 8');
        $this->assertArrayNotHasKey('attendanceRequired', $events[8], 'attendanceRequired is correct for event 8');

        $this->assertEquals($events[9]['offering'], 1);
        $this->assertEquals($events[9]['startDate'], $offerings[0]['startDate']);
        $this->assertEquals($events[9]['endDate'], $offerings[0]['endDate']);
        $this->assertEquals($events[9]['courseTitle'], $courses[0]['title']);
        $this->assertFalse($events[9]['attireRequired'], 'attireRequired is correct for event 9');
        $this->assertArrayNotHasKey('equipmentRequired', $events[9], 'equipmentRequired is correct for event 9');
        $this->assertFalse($events[9]['supplemental'], 'supplemental is correct for event 9');
        $this->assertArrayNotHasKey('attendanceRequired', $events[9], 'attendanceRequired is correct for event 9');

        $this->assertEquals(8, $events[10]['offering']);

        /** @var OfferingInterface $offering */
        $offering = $this->fixtures->getReference('offerings8');
        $this->assertEquals($events[10]['startDate'], $offering->getStartDate()->format('c'));
        $this->assertEquals($events[10]['endDate'], $offering->getEndDate()->format('c'));
        $this->assertEquals($events[10]['courseTitle'], $courses[1]['title']);
        $this->assertFalse($events[10]['attireRequired'], 'attireRequired is correct for event 10');
        $this->assertFalse($events[10]['equipmentRequired'], 'equipmentRequired is correct for event 10');
        $this->assertTrue($events[10]['supplemental'], 'supplemental is correct for event 10');
        $this->assertArrayNotHasKey('attendanceRequired', $events[10], 'attendanceRequired is correct for event 10');


        foreach ($events as $event) {
            $this->assertEquals($school['id'], $event['school']);
        }
    }

    public function testMultidayEvent()
    {
        $school = $this->container->get(SchoolData::class)->getOne();
        $offerings = $this->container->get(OfferingData::class)->getAll();
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');

        $events = $this->getEvents($school['id'], $from->getTimestamp(), $to->getTimestamp());
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }

    protected function getEvents($schoolId, $from, $to)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $schoolId,
            'from' => $from,
            'to' => $to
        ];
        $url = $this->getUrl(
            'ilios_api_schoolevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['events'];
    }
}
