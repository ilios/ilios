<?php

namespace App\Tests\Endpoints;

use App\Entity\OfferingInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\IlmSessionData;
use App\Tests\DataLoader\OfferingData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionDescriptionData;
use App\Tests\DataLoader\SessionTypeData;
use App\Tests\AbstractEndpointTest;
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
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadSessionDescriptionData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
        ];
    }

    public function testAttachedUserMaterials()
    {
        $userId = 5;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->getTokenForUser(5)
        );
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

    public function testAttachedMaterialsRemovedIfUserIsNotOwnerOfRequestedEvents()
    {
        $userId = 5;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->getTokenForUser(2)
        );
        $lms = $events[0]['learningMaterials'];

        $this->assertEquals(0, count($lms));
    }

    public function testGetEvents()
    {
        $offerings = $this->getContainer()->get(OfferingData::class)->getAll();
        $sessionTypes = $this->getContainer()->get(SessionTypeData::class)->getAll();
        $sessionDescriptions = $this->getContainer()->get(SessionDescriptionData::class)->getAll();
        $ilmSessions = $this->getContainer()->get(IlmSessionData::class)->getAll();
        $courses = $this->getContainer()->get(CourseData::class)->getAll();
        $sessions = $this->getContainer()->get(SessionData::class)->getAll();

        $userId = 2;

        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->getAuthenticatedUserToken()
        );

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
            $events[0]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 0'
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
        $this->assertEquals([
            'id' => 4,
            'title' => $sessions[3]['title']
        ], $events[0]['postrequisiteSession']);
        $this->assertEmpty($events[0]['prerequisiteSessions']);

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
            $events[1]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 1'
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
        $this->assertEquals($events[2]['postrequisiteSession'], [
            'id' => 4,
            'title' => $sessions[3]['title']
        ]);
        $this->assertEmpty($events[2]['prerequisiteSessions']);

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
            $events[2]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 2'
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
        $this->assertEquals($events[2]['postrequisiteSession'], [
            'id' => 4,
            'title' => $sessions[3]['title']
        ]);
        $this->assertEmpty($events[2]['prerequisiteSessions']);

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
        $this->assertEquals(
            $events[3]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 3'
        );
        $this->assertEquals(
            7,
            count($events[3]['learningMaterials']),
            'Event 3 has the correct number of learning materials'
        );

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
        $this->assertEquals($events[3]['postrequisiteSession'], [
            'id' => 4,
            'title' => $sessions[3]['title']
        ]);
        $this->assertEmpty($events[3]['prerequisiteSessions']);

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
        $this->assertEquals(
            $events[4]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 4'
        );
        $this->assertEquals(
            7,
            count($events[4]['learningMaterials']),
            'Event 4 has the correct number of learning materials'
        );

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
        $this->assertEquals($events[4]['postrequisiteSession'], [
            'id' => 4,
            'title' => $sessions[3]['title']
        ]);
        $this->assertEmpty($events[4]['prerequisiteSessions']);

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
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[5],
            'instructional notes is correct for event 5'
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
        $this->assertArrayNotHasKey('postrequisiteSession', $events[5]);
        $this->assertEmpty($events[5]['prerequisiteSessions']);

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
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[6],
            'instructional notes is correct for event 6'
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
        $this->assertArrayNotHasKey('postrequisiteSession', $events[6]);
        $this->assertEmpty($events[6]['prerequisiteSessions']);

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
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[7],
            'instructional notes is correct for event 7'
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
        $this->assertArrayNotHasKey('postrequisiteSession', $events[7]);
        $this->assertEmpty($events[7]['prerequisiteSessions']);

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
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[8],
            'instructional notes is correct for event 8'
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
        $this->assertArrayNotHasKey('postrequisiteSession', $events[8]);
        $this->assertEmpty($events[8]['prerequisiteSessions']);

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
            $events[9]['instructionalNotes'],
            $sessions[0]['instructionalNotes'],
            'instructional notes is correct for event 9'
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
        $this->assertArrayNotHasKey('postrequisiteSession', $events[9]);
        $this->assertEmpty($events[9]['prerequisiteSessions']);

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
            $events[10]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 10'
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
        $this->assertEquals($events[10]['postrequisiteSession'], [
            'id' => 4,
            'title' => $sessions[3]['title']
        ]);
        $this->assertEmpty($events[10]['prerequisiteSessions']);

        foreach ($events as $event) {
            $this->assertEquals($userId, $event['user']);
        }
    }

    public function testMultidayEvent()
    {
        $offerings = $this->getContainer()->get(OfferingData::class)->getAll();
        $userId = 2;
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');

        $events = $this->getEvents(
            $userId,
            $from->getTimestamp(),
            $to->getTimestamp(),
            $this->getAuthenticatedUserToken()
        );
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }

    public function testPrivilegedUsersGetsEventsForUnpublishedSessions()
    {
        $userId = 2;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->getTokenForUser($userId)
        );
        $event = $events[3];
        $this->assertFalse($event['isPublished']);
        $this->assertFalse($event['isScheduled']);
        $lms = $event['learningMaterials'];

        $this->assertEquals(7, count($lms));
        $this->assertEquals('2', $lms[0]['sessionLearningMaterial']);
        $this->assertEquals('3', $lms[1]['sessionLearningMaterial']);
        $this->assertEquals('4', $lms[2]['sessionLearningMaterial']);
        $this->assertEquals('5', $lms[3]['sessionLearningMaterial']);
        $this->assertEquals('6', $lms[4]['sessionLearningMaterial']);
        $this->assertEquals('7', $lms[5]['sessionLearningMaterial']);
        $this->assertEquals('8', $lms[6]['sessionLearningMaterial']);
    }

    public function testGetEventsBySessionForCourseDirector()
    {
        $userId = 2;
        $sessionId = 3;

        $events = $this->getEventsForSessionId(
            $userId,
            $sessionId,
            $this->getTokenForUser($userId)
        );

        $this->assertEquals(3, count($events), 'Expected events returned');
        $this->assertEquals(
            $sessionId,
            $events[0]['session']
        );
        $this->assertEquals(6, $events[0]['offering']);
        $this->assertEquals($sessionId, $events[0]['session']);
        $this->assertEquals(7, $events[1]['offering']);
        $this->assertEquals($sessionId, $events[1]['session']);
        $this->assertEquals(8, $events[2]['offering']);
        $this->assertEquals($sessionId, $events[2]['session']);
    }

    public function testGetEventsBySessionForLearner()
    {
        $userId = 5;
        $sessionId = 1;

        $events = $this->getEventsForSessionId(
            $userId,
            $sessionId,
            $this->getTokenForUser($userId)
        );

        $this->assertEquals(2, count($events), 'Expected events returned');
        $this->assertEquals(
            $sessionId,
            $events[0]['session']
        );
        $this->assertEquals(1, $events[0]['offering']);
        $this->assertEquals($sessionId, $events[0]['session']);
        $this->assertEquals(2, $events[1]['offering']);
        $this->assertEquals($sessionId, $events[1]['session']);
    }

    /**
     * @param int $userId
     * @param int $from
     * @param int $to
     * @param string|null $userToken
     * @return array
     */
    protected function getEvents($userId, $from, $to, $userToken)
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
            $userToken
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)['userEvents'];
    }

    /**
     * @param int $userId
     * @param int $from
     * @param int $to
     * @param string|null $userToken
     * @return array
     */
    protected function getEventsForSessionId($userId, $sessionId, $userToken)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $userId,
            'session' => $sessionId,
        ];
        $url = $this->getUrl(
            'ilios_api_userevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $userToken
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)['userEvents'];
    }
}
