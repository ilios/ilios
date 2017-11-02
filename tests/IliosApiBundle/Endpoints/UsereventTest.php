<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\OfferingInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\CourseData;
use Tests\CoreBundle\DataLoader\IlmSessionData;
use Tests\CoreBundle\DataLoader\LearningMaterialData;
use Tests\CoreBundle\DataLoader\OfferingData;
use Tests\CoreBundle\DataLoader\SessionDescriptionData;
use Tests\CoreBundle\DataLoader\SessionTypeData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use DateTime;

/**
 * UsereventTest API endpoint Test.
 * @group api_1
 */
class UsereventTest extends AbstractEndpointTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
        ];
    }

    public function testAttachedUserMaterials()
    {
        $userId = 5;
        $events = $this->getEvents($userId, 0, 100000000000);
        $lms = $events[0]['learningMaterials'];

        $this->assertEquals(9, count($lms));
        $this->assertEquals(15, count($lms[0]));
        $this->assertEquals('1', $lms[0]['id']);
        $this->assertEquals('1', $lms[0]['sessionLearningMaterial']);
        $this->assertEquals('1', $lms[0]['session']);
        $this->assertEquals('1', $lms[0]['course']);
        $this->assertEquals('1', $lms[0]['position']);
        $this->assertTrue($lms[0]['required']);
        $this->assertStringStartsWith('firstlm', $lms[0]['title']);
        $this->assertEquals('desc1', $lms[0]['description']);
        $this->assertEquals('author1', $lms[0]['originalAuthor']);
        $this->assertEquals('citation1', $lms[0]['citation']);
        $this->assertEquals('citation', $lms[0]['mimetype']);
        $this->assertEquals('session1Title', $lms[0]['sessionTitle']);
        $this->assertEquals('firstCourse', $lms[0]['courseTitle']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $lms[5]['firstOfferingDate']);
        $this->assertEquals(0, count($lms[0]['instructors']));
        $this->assertFalse($lms[0]['isBlanked']);

        $this->assertEquals(15, count($lms[1]));
        $this->assertFalse($lms[1]['isBlanked']);

        $this->assertEquals(17, count($lms[2]));
        $this->assertFalse($lms[2]['isBlanked']);

        $this->assertEquals(18, count($lms[3]));
        $this->assertFalse($lms[3]['isBlanked']);

        $this->assertEquals(10, count($lms[4]));
        $this->assertEquals('6', $lms[4]['id']);
        $this->assertEquals('6', $lms[4]['courseLearningMaterial']);
        $this->assertEquals('1', $lms[4]['course']);
        $this->assertEquals('4', $lms[4]['position']);
        $this->assertEquals('sixthlm', $lms[4]['title']);
        $this->assertEquals('firstCourse', $lms[4]['courseTitle']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $lms[4]['firstOfferingDate']);
        $this->assertEmpty($lms[4]['instructors']);
        $this->assertNotEmpty($lms[4]['startDate']);
        $this->assertTrue($lms[4]['isBlanked']);

        $this->assertEquals(18, count($lms[5]));
        $this->assertNotEmpty($lms[5]['endDate']);
        $this->assertFalse($lms[5]['isBlanked']);

        $this->assertEquals(10, count($lms[6]));
        $this->assertNotEmpty($lms[6]['endDate']);
        $this->assertTrue($lms[6]['isBlanked']);

        $this->assertEquals(19, count($lms[7]));
        $this->assertNotEmpty($lms[7]['startDate']);
        $this->assertNotEmpty($lms[7]['endDate']);
        $this->assertFalse($lms[7]['isBlanked']);

        $this->assertEquals(11, count($lms[8]));
        $this->assertNotEmpty($lms[8]['startDate']);
        $this->assertNotEmpty($lms[8]['endDate']);
        $this->assertTrue($lms[8]['isBlanked']);
    }

    public function testGetEvents()
    {
        $offerings = $this->container->get(OfferingData::class)->getAll();
        $sessionTypes = $this->container->get(SessionTypeData::class)->getAll();
        $sessionDescriptions = $this->container->get(SessionDescriptionData::class)->getAll();
        $ilmSessions = $this->container->get(IlmSessionData::class)->getAll();
        $courses = $this->container->get(CourseData::class)->getAll();

        $userId = 2;

        $events = $this->getEvents($userId, 0, 100000000000);

        $this->assertEquals(12, count($events), 'Expected events returned');
        $this->assertEquals(
            $events[0]['offering'],
            3,
            'offering is correct for event 0'
        );
        $this->assertEquals(
            $events[0]['startDate'],
            $offerings[2]['startDate'],
            'startDate is correct for event 0'
        );
        $this->assertEquals(
            $events[0]['endDate'],
            $offerings[2]['endDate'],
            'endDate is correct for event 0'
        );
        $this->assertEquals($events[0]['courseTitle'], $courses[0]['title'], 'title is correct for event 0');
        $this->assertEquals(
            $events[0]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 0'
        );
        $this->assertEquals(
            $events[0]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 0'
        );
        $this->assertEquals(
            $events[0]['sessionDescription'],
            $sessionDescriptions[1]['description'],
            'session description is correct for event 0'
        );
        $this->assertEquals(
            count($events[0]['learningMaterials']),
            9,
            'Event 0 has the correct number of learning materials'
        );
        $this->assertTrue(
            $events[0]['attireRequired'],
            'attire is correct for event 0'
        );
        $this->assertTrue(
            $events[0]['equipmentRequired'],
            'equipmentRequired is correct for event 0'
        );
        $this->assertTrue(
            $events[0]['supplemental'],
            'supplemental is correct for event 0'
        );
        $this->assertTrue(
            $events[0]['attendanceRequired'],
            'attendanceRequired is correct for event 0'
        );

        $this->assertEquals($events[1]['offering'], 4, 'offering is correct for event 1');
        $this->assertEquals(
            $events[1]['startDate'],
            $offerings[3]['startDate'],
            'startDate is correct for event 1'
        );
        $this->assertEquals($events[1]['endDate'], $offerings[3]['endDate'], 'endDate is correct for event 1');
        $this->assertEquals($events[1]['courseTitle'], $courses[0]['title'], 'title is correct for event 1');
        $this->assertEquals(
            $events[1]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 1'
        );
        $this->assertEquals(
            $events[1]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 1'
        );
        $this->assertEquals(
            $events[1]['sessionDescription'],
            $sessionDescriptions[1]['description'],
            'session description is correct for event 1'
        );
        $this->assertEquals(
            count($events[1]['learningMaterials']),
            9,
            'Event 1 has the correct number of learning materials'
        );
        $this->assertTrue(
            $events[1]['attireRequired'],
            'attire is correct for event 1'
        );
        $this->assertTrue(
            $events[1]['equipmentRequired'],
            'equipmentRequired is correct for event 1'
        );
        $this->assertTrue(
            $events[1]['supplemental'],
            'supplemental is correct for event 1'
        );
        $this->assertTrue(
            $events[1]['attendanceRequired'],
            'attendanceRequired is correct for event 1'
        );

        $this->assertEquals($events[2]['offering'], 5, 'offering is correct for event 2');
        $this->assertEquals(
            $events[2]['startDate'],
            $offerings[4]['startDate'],
            'startDate is correct for event 2'
        );
        $this->assertEquals($events[2]['endDate'], $offerings[4]['endDate'], 'endDate is correct for event 2');
        $this->assertEquals($events[2]['courseTitle'], $courses[0]['title'], 'title is correct for event 2');
        $this->assertEquals(
            $events[2]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 2'
        );
        $this->assertEquals(
            $events[2]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 2'
        );
        $this->assertEquals(
            $events[2]['sessionDescription'],
            $sessionDescriptions[1]['description'],
            'session description is correct for event 2'
        );
        $this->assertEquals(
            count($events[2]['learningMaterials']),
            9,
            'Event 2 has the correct number of learning materials'
        );
        $this->assertTrue(
            $events[2]['attireRequired'],
            'attire is correct for event 2'
        );
        $this->assertTrue(
            $events[2]['equipmentRequired'],
            'equipmentRequired is correct for event 2'
        );
        $this->assertTrue(
            $events[2]['supplemental'],
            'supplemental is correct for event 2'
        );
        $this->assertTrue(
            $events[2]['attendanceRequired'],
            'attendanceRequired is correct for event 2'
        );

        $this->assertEquals($events[3]['offering'], 6, 'offering is correct for event 3');
        $this->assertEquals($events[3]['startDate'], $offerings[5]['startDate'], 'startDate is correct for event 3');
        $this->assertEquals($events[3]['endDate'], $offerings[5]['endDate'], 'endDate is correct for event 3');
        $this->assertEquals($events[3]['courseTitle'], $courses[1]['title'], 'title is correct for event 3');
        $this->assertEquals(
            $events[3]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 3'
        );
        $this->assertEquals(
            $events[3]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 3'
        );
        $this->assertEquals(count($events[3]['learningMaterials']), 0, 'Event 3 has no learning materials');

        $this->assertFalse(
            $events[3]['attireRequired'],
            'attire is correct for event 3'
        );
        $this->assertFalse(
            $events[3]['equipmentRequired'],
            'equipmentRequired is correct for event 3'
        );
        $this->assertTrue(
            $events[3]['supplemental'],
            'supplemental is correct for event 3'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[3],
            'attendanceRequired is correct for event 3'
        );

        $this->assertEquals($events[4]['offering'], 7, 'offering is correct for event 4');
        $this->assertEquals($events[4]['startDate'], $offerings[6]['startDate'], 'startDate is correct for event 4');
        $this->assertEquals($events[4]['endDate'], $offerings[6]['endDate'], 'endDate is correct for event 4');
        $this->assertEquals($events[4]['courseTitle'], $courses[1]['title'], 'title is correct for event 4');
        $this->assertEquals(
            $events[4]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 4'
        );
        $this->assertEquals(
            $events[4]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 4'
        );
        $this->assertEquals(count($events[4]['learningMaterials']), 0, 'Event 4 has no learning materials');

        $this->assertFalse(
            $events[4]['attireRequired'],
            'attire is correct for event 4'
        );
        $this->assertFalse(
            $events[4]['equipmentRequired'],
            'equipmentRequired is correct for event 4'
        );
        $this->assertTrue(
            $events[4]['supplemental'],
            'supplemental is correct for event 4'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[4],
            'attendanceRequired is correct for event 4'
        );

        $this->assertEquals($events[5]['ilmSession'], 1, 'ilmSession is correct for 5');
        $this->assertEquals($events[5]['startDate'], $ilmSessions[0]['dueDate'], 'dueDate is correct for 5');
        $this->assertEquals($events[5]['courseTitle'], $courses[1]['title'], 'title is correct for 5');
        $this->assertEquals(
            $events[5]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 5'
        );
        $this->assertEquals(
            $events[5]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 5'
        );
        $this->assertEquals(count($events[5]['learningMaterials']), 0, 'Event 5 has no learning materials');

        $this->assertFalse(
            $events[5]['attireRequired'],
            'attire is correct for event 5'
        );
        $this->assertFalse(
            $events[5]['equipmentRequired'],
            'equipmentRequired is correct for event 5'
        );
        $this->assertFalse(
            $events[5]['supplemental'],
            'supplemental is correct for event 5'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[5],
            'attendanceRequired is correct for event 5'
        );

        $this->assertEquals($events[6]['ilmSession'], 2, 'ilmSession is correct for event 6');
        $this->assertEquals($events[6]['startDate'], $ilmSessions[1]['dueDate'], 'dueDate is correct for event 6');
        $this->assertEquals($events[6]['courseTitle'], $courses[1]['title'], 'ilmSession is correct for event 6');
        $this->assertEquals(
            $events[6]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 6'
        );
        $this->assertEquals(
            $events[6]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 6'
        );
        $this->assertEquals(count($events[6]['learningMaterials']), 0, 'Event 6 has no learning materials');
        $this->assertFalse(
            $events[6]['attireRequired'],
            'attire is correct for event 6'
        );
        $this->assertFalse(
            $events[6]['equipmentRequired'],
            'equipmentRequired is correct for event 6'
        );
        $this->assertFalse(
            $events[6]['supplemental'],
            'supplemental is correct for event 6'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[6],
            'attendanceRequired is correct for event 6'
        );

        $this->assertEquals($events[7]['ilmSession'], 3, 'ilmSession is correct for event 7');
        $this->assertEquals($events[7]['startDate'], $ilmSessions[2]['dueDate'], 'dueDate is correct for event 7');
        $this->assertEquals($events[7]['courseTitle'], $courses[1]['title'], 'title is correct for event 7');
        $this->assertEquals(
            $events[7]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 7'
        );
        $this->assertEquals(
            $events[7]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 7'
        );
        $this->assertEquals(count($events[7]['learningMaterials']), 0, 'Event 7 has no learning materials');
        $this->assertFalse(
            $events[7]['attireRequired'],
            'attire is correct for event 7'
        );
        $this->assertFalse(
            $events[7]['equipmentRequired'],
            'equipmentRequired is correct for event 7'
        );
        $this->assertFalse(
            $events[7]['supplemental'],
            'supplemental is correct for event 7'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[7],
            'attendanceRequired is correct for event 7'
        );

        $this->assertEquals($events[8]['ilmSession'], 4, 'ilmSession is correct for event 8');
        $this->assertEquals($events[8]['startDate'], $ilmSessions[3]['dueDate'], 'dueDate is correct for event 8');
        $this->assertEquals($events[8]['courseTitle'], $courses[1]['title'], 'title is correct for event 8');
        $this->assertEquals(
            $events[8]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 8'
        );
        $this->assertEquals(
            $events[8]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 8'
        );
        $this->assertEquals(count($events[8]['learningMaterials']), 0, 'Event 8 has no learning materials');
        $this->assertFalse(
            $events[8]['attireRequired'],
            'attire is correct for event 8'
        );
        $this->assertFalse(
            $events[8]['equipmentRequired'],
            'equipmentRequired is correct for event 8'
        );
        $this->assertFalse(
            $events[8]['supplemental'],
            'supplemental is correct for event 8'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[8],
            'attendanceRequired is correct for event 8'
        );

        $this->assertEquals($events[9]['startDate'], $offerings[0]['startDate'], 'startDate is correct for event 9');
        $this->assertEquals($events[9]['endDate'], $offerings[0]['endDate'], 'endDate is correct for event 9');
        $this->assertEquals(
            $events[9]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 9'
        );
        $this->assertEquals(
            $events[9]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 9'
        );
        $this->assertEquals(
            $events[9]['sessionDescription'],
            $sessionDescriptions[0]['description'],
            'session description is correct for event 9'
        );
        $this->assertEquals(
            count($events[9]['learningMaterials']),
            10,
            'Event 6 has the correct number of learning materials'
        );
        $this->assertFalse(
            $events[9]['attireRequired'],
            'attire is correct for event 9'
        );
        $this->assertArrayNotHasKey(
            'equipmentRequired',
            $events[9],
            'equipmentRequired is correct for event 9'
        );
        $this->assertFalse(
            $events[9]['supplemental'],
            'supplemental is correct for event 9'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[9],
            'attendanceRequired is correct for event 9'
        );

        /** @var OfferingInterface $offering */
        $offering = $this->fixtures->getReference('offerings8');
        $this->assertEquals(8, $events[10]['offering'], 'offering is correct for event 10');
        $this->assertEquals(
            $events[10]['startDate'],
            $offering->getStartDate()->format('c'),
            'startDate is correct for event 10'
        );
        $this->assertEquals(
            $events[10]['endDate'],
            $offering->getEndDate()->format('c'),
            'endDate is correct for event 10'
        );
        $this->assertEquals(
            $events[10]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 10'
        );
        $this->assertEquals(
            $events[10]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 10'
        );
        $this->assertFalse(
            $events[10]['attireRequired'],
            'attire is correct for event 10'
        );
        $this->assertFalse(
            $events[10]['equipmentRequired'],
            'equipmentRequired is correct for event 10'
        );
        $this->assertTrue(
            $events[10]['supplemental'],
            'supplemental is correct for event 10'
        );
        $this->assertArrayNotHasKey(
            'attendanceRequired',
            $events[10],
            'attendanceRequired is correct for event 10'
        );

        foreach ($events as $event) {
            $this->assertEquals($userId, $event['user']);
        }
    }

    public function testMultidayEvent()
    {
        $offerings = $this->container->get(OfferingData::class)->getAll();
        $userId = 2;
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');

        $events = $this->getEvents($userId, $from->getTimestamp(), $to->getTimestamp());
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }

    public function testWhenViewingAnotherUsersEventsOnlyPublishedShows()
    {
        $userId = 1;
        $from = new DateTime('2015-01-01 00:00:00');
        $to = new DateTime('2015-02-30 23:59:59');

        $events = $this->getEvents($userId, $from->getTimestamp(), $to->getTimestamp());
        $this->assertEquals(0, count($events), 'Expected events returned');
    }

    protected function getEvents($userId, $from, $to)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $userId,
            'from' => $from,
            'to' => $to,
        ];
        $url = $this->getUrl(
            'ilios_api_userevents',
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

        return json_decode($response->getContent(), true)['userEvents'];
    }
}
