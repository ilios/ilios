<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Classes\CalendarEventUserContext;
use App\Entity\Offering;
use App\Entity\OfferingInterface;
use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\IlmSessionData;
use App\Tests\DataLoader\OfferingData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionTypeData;
use App\Tests\DataLoader\UserData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadUserData;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

/**
 * UsereventTest API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class UsereventTest extends AbstractEndpoint
{
    protected function getFixtures(): array
    {
        return [
            LoadOfferingData::class,
            LoadIlmSessionData::class,
            LoadUserData::class,
            LoadSessionData::class,
            LoadLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
        ];
    }

    public function testAttachedUserMaterials(): void
    {
        $userId = 5;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->createJwtFromUserId($this->kernelBrowser, 5)
        );
        $lms = $events[0]['learningMaterials'];

        $this->assertSame(9, count($lms));
        $this->assertSame(17, count($lms[0]));
        $this->assertSame('1', $lms[0]['id']);
        $this->assertSame('1', $lms[0]['sessionLearningMaterial']);
        $this->assertSame('1', $lms[0]['session']);
        $this->assertSame('1', $lms[0]['course']);
        $this->assertSame('1', $lms[0]['position']);
        $this->assertTrue($lms[0]['required']);
        $this->assertStringStartsWith('firstlm', $lms[0]['title']);
        $this->assertSame('desc1', $lms[0]['description']);
        $this->assertSame('author1', $lms[0]['originalAuthor']);
        $this->assertSame('citation1', $lms[0]['citation']);
        $this->assertSame('citation', $lms[0]['mimetype']);
        $this->assertSame('session1Title', $lms[0]['sessionTitle']);
        $this->assertSame('firstCourse', $lms[0]['courseTitle']);
        $this->assertSame(2016, $lms[0]['courseYear']);
        $this->assertSame('first', $lms[0]['courseExternalId']);
        $this->assertSame('2016-09-04T00:00:00+00:00', $lms[5]['firstOfferingDate']);
        $this->assertSame(0, count($lms[0]['instructors']));
        $this->assertFalse($lms[0]['isBlanked']);

        $this->assertSame(17, count($lms[1]));
        $this->assertFalse($lms[1]['isBlanked']);

        $this->assertSame(19, count($lms[2]));
        $this->assertFalse($lms[2]['isBlanked']);

        $this->assertSame(20, count($lms[3]));
        $this->assertFalse($lms[3]['isBlanked']);

        $this->assertSame(12, count($lms[4]));
        $this->assertSame('6', $lms[4]['id']);
        $this->assertSame('6', $lms[4]['courseLearningMaterial']);
        $this->assertSame('1', $lms[4]['course']);
        $this->assertSame('4', $lms[4]['position']);
        $this->assertSame('sixthlm', $lms[4]['title']);
        $this->assertSame('firstCourse', $lms[4]['courseTitle']);
        $this->assertSame('2016-09-04T00:00:00+00:00', $lms[4]['firstOfferingDate']);
        $this->assertEmpty($lms[4]['instructors']);
        $this->assertNotEmpty($lms[4]['startDate']);
        $this->assertTrue($lms[4]['isBlanked']);

        $this->assertSame(20, count($lms[5]));
        $this->assertNotEmpty($lms[5]['endDate']);
        $this->assertFalse($lms[5]['isBlanked']);

        $this->assertSame(12, count($lms[6]));
        $this->assertNotEmpty($lms[6]['endDate']);
        $this->assertTrue($lms[6]['isBlanked']);

        $this->assertSame(21, count($lms[7]));
        $this->assertNotEmpty($lms[7]['startDate']);
        $this->assertNotEmpty($lms[7]['endDate']);
        $this->assertFalse($lms[7]['isBlanked']);

        $this->assertSame(13, count($lms[8]));
        $this->assertNotEmpty($lms[8]['startDate']);
        $this->assertNotEmpty($lms[8]['endDate']);
        $this->assertTrue($lms[8]['isBlanked']);
    }

    public function testGetEvents(): void
    {
        $offerings = self::getContainer()->get(OfferingData::class)->getAll();
        $sessionTypes = self::getContainer()->get(SessionTypeData::class)->getAll();
        $ilmSessions = self::getContainer()->get(IlmSessionData::class)->getAll();
        $courses = self::getContainer()->get(CourseData::class)->getAll();
        $sessions = self::getContainer()->get(SessionData::class)->getAll();

        $userId = 2;

        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $this->assertSame(12, count($events), 'Expected events returned');
        $this->assertSame(
            $events[0]['offering'],
            3,
            'offering is correct for event 0'
        );
        $this->assertSame(
            $events[0]['session'],
            2,
            'session is correct for event 0'
        );
        $this->assertSame(
            $events[0]['startDate'],
            $offerings[2]['startDate'],
            'startDate is correct for event 0'
        );
        $this->assertSame(
            $events[0]['endDate'],
            $offerings[2]['endDate'],
            'endDate is correct for event 0'
        );
        $this->assertArrayNotHasKey('url', $events[0]);
        $this->assertSame($events[0]['courseTitle'], $courses[0]['title'], 'title is correct for event 0');
        $this->assertSame($events[0]['course'], $courses[0]['id'], 'id is correct for event 0');
        $this->assertSame(
            $events[0]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 0'
        );
        $this->assertSame(
            $events[0]['courseLevel'],
            $courses[0]['level'],
            'course level correct for event 0'
        );
        $this->assertEquals(
            array_column($events[0]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 0'
        );
        $this->assertEquals(
            array_column($events[0]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 0'
        );

        $this->assertSame(
            $events[0]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 0'
        );
        $this->assertSame(
            $events[0]['sessionTypeId'],
            $sessionTypes[1]['id'],
            'session type id is correct for event 0'
        );
        $this->assertSame(
            $events[0]['sessionDescription'],
            $sessions[1]['description'],
            'session description is correct for event 0'
        );
        $this->assertSame(
            $events[0]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 0'
        );
        $this->assertEquals(
            array_column($events[0]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
            count($events[0]['learningMaterials']),
            10,
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

        $this->assertSame(1, count($events[0]['postrequisites']));
        $this->assertSame(6, $events[0]['postrequisites'][0]['offering']);
        $this->assertSame(3, $events[0]['postrequisites'][0]['session']);
        $this->assertEmpty($events[0]['prerequisites']);
        $this->assertCount(1, $events[0]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[0]['userContexts'][0]);

        $this->assertSame($events[1]['offering'], 4, 'offering is correct for event 1');
        $this->assertSame(
            $events[1]['startDate'],
            $offerings[3]['startDate'],
            'startDate is correct for event 1'
        );
        $this->assertSame($events[1]['endDate'], $offerings[3]['endDate'], 'endDate is correct for event 1');
        $this->assertArrayNotHasKey('url', $events[1]);
        $this->assertSame($events[1]['courseTitle'], $courses[0]['title'], 'title is correct for event 1');
        $this->assertSame($events[1]['course'], $courses[0]['id'], 'id is correct for event 1');
        $this->assertSame(
            $events[1]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 1'
        );
        $this->assertSame(
            $events[1]['courseLevel'],
            $courses[0]['level'],
            'course level correct for event 1'
        );
        $this->assertEquals(
            array_column($events[1]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 1'
        );
        $this->assertEquals(
            array_column($events[1]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 1'
        );
        $this->assertSame(
            $events[1]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 1'
        );
        $this->assertSame(
            $events[1]['sessionTypeId'],
            $sessionTypes[1]['id'],
            'session type id is correct for event 1'
        );
        $this->assertSame(
            $events[1]['sessionDescription'],
            $sessions[1]['description'],
            'session description is correct for event 1'
        );
        $this->assertSame(
            $events[1]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 1'
        );
        $this->assertEquals(
            array_column($events[1]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
            count($events[1]['learningMaterials']),
            10,
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
        $this->assertSame(1, count($events[1]['postrequisites']));
        $this->assertSame(6, $events[1]['postrequisites'][0]['offering']);
        $this->assertSame(3, $events[1]['postrequisites'][0]['session']);
        $this->assertEmpty($events[1]['prerequisites']);
        $this->assertCount(1, $events[1]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::LEARNER, $events[1]['userContexts'][0]);

        $this->assertSame($events[2]['offering'], 5, 'offering is correct for event 2');
        $this->assertSame(
            $events[2]['startDate'],
            $offerings[4]['startDate'],
            'startDate is correct for event 2'
        );
        $this->assertSame($events[2]['endDate'], $offerings[4]['endDate'], 'endDate is correct for event 2');
        $this->assertSame(
            $events[2]['url'],
            $offerings[4]['url'],
            'url is correct for event 2'
        );
        $this->assertSame($events[2]['courseTitle'], $courses[0]['title'], 'title is correct for event 2');
        $this->assertSame($events[2]['course'], $courses[0]['id'], 'id is correct for event 2');
        $this->assertSame(
            $events[2]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 2'
        );
        $this->assertSame(
            $events[2]['courseLevel'],
            $courses[0]['level'],
            'course level correct for event 2'
        );
        $this->assertEquals(
            array_column($events[2]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 2'
        );
        $this->assertEquals(
            array_column($events[2]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 2'
        );
        $this->assertSame(
            $events[2]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 2'
        );
        $this->assertSame(
            $events[2]['sessionTypeId'],
            $sessionTypes[1]['id'],
            'session type id is correct for event 2'
        );
        $this->assertSame(
            $events[2]['sessionDescription'],
            $sessions[1]['description'],
            'session description is correct for event 2'
        );
        $this->assertSame(
            $events[2]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 2'
        );
        $this->assertEquals(
            array_column($events[2]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
            count($events[2]['learningMaterials']),
            10,
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
        $this->assertSame(1, count($events[2]['postrequisites']));
        $this->assertSame(6, $events[2]['postrequisites'][0]['offering']);
        $this->assertSame(3, $events[2]['postrequisites'][0]['session']);
        $this->assertEmpty($events[2]['prerequisites']);
        $this->assertCount(1, $events[2]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[2]['userContexts'][0]);

        $this->assertSame($events[3]['offering'], 6, 'offering is correct for event 3');
        $this->assertSame($events[3]['startDate'], $offerings[5]['startDate'], 'startDate is correct for event 3');
        $this->assertSame($events[3]['endDate'], $offerings[5]['endDate'], 'endDate is correct for event 3');
        $this->assertArrayNotHasKey('url', $events[3]);
        $this->assertSame($events[3]['courseTitle'], $courses[1]['title'], 'title is correct for event 3');
        $this->assertSame($events[3]['course'], $courses[1]['id'], 'id is correct for event 3');
        $this->assertSame(
            $events[3]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 3'
        );
        $this->assertSame(
            $events[3]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 3'
        );
        $this->assertEquals(
            array_column($events[3]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 3'
        );
        $this->assertEquals(
            array_column($events[3]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 3'
        );
        $this->assertSame(
            $events[3]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 3'
        );
        $this->assertSame(
            $events[3]['sessionTypeId'],
            $sessionTypes[1]['id'],
            'session type id is correct for event 3'
        );
        $this->assertSame(
            $events[3]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 3'
        );
        $this->assertEquals(
            array_column($events[3]['sessionTerms'], 'id'),
            $sessions[2]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
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
        $this->assertEmpty($events[3]['postrequisites']);
        $this->assertSame(3, count($events[3]['prerequisites']));
        $sessionIds = array_unique(array_column($events[3]['prerequisites'], 'session'));
        sort($sessionIds);
        $this->assertSame([2], $sessionIds);
        $this->assertCount(1, $events[3]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[3]['userContexts'][0]);

        $this->assertSame($events[4]['offering'], 7, 'offering is correct for event 4');
        $this->assertSame($events[4]['startDate'], $offerings[6]['startDate'], 'startDate is correct for event 4');
        $this->assertSame($events[4]['endDate'], $offerings[6]['endDate'], 'endDate is correct for event 4');
        $this->assertArrayNotHasKey('url', $events[4]);
        $this->assertSame($events[4]['courseTitle'], $courses[1]['title'], 'title is correct for event 4');
        $this->assertSame($events[4]['course'], $courses[1]['id'], 'id is correct for event 4');
        $this->assertSame(
            $events[4]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 4'
        );
        $this->assertSame(
            $events[4]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 4'
        );
        $this->assertEquals(
            array_column($events[4]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 4'
        );
        $this->assertEquals(
            array_column($events[4]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 4'
        );
        $this->assertSame(
            $events[4]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 4'
        );
        $this->assertSame(
            $events[4]['sessionTypeId'],
            $sessionTypes[1]['id'],
            'session type id is correct for event 4'
        );
        $this->assertSame(
            $events[4]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 4'
        );
        $this->assertEquals(
            array_column($events[4]['sessionTerms'], 'id'),
            $sessions[2]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
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
        $this->assertEmpty($events[4]['postrequisites']);
        $this->assertSame(3, count($events[4]['prerequisites']));
        $sessionIds = array_unique(array_column($events[4]['prerequisites'], 'session'));
        sort($sessionIds);
        $this->assertSame([2], $sessionIds);
        $this->assertCount(1, $events[4]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[4]['userContexts'][0]);

        $this->assertSame($events[5]['ilmSession'], 1, 'ilmSession is correct for 5');
        $this->assertSame($events[5]['startDate'], $ilmSessions[0]['dueDate'], 'dueDate is correct for 5');
        $this->assertSame($events[5]['courseTitle'], $courses[1]['title'], 'title is correct for 5');
        $this->assertArrayNotHasKey('url', $events[5]);
        $this->assertSame($events[5]['course'], $courses[1]['id'], 'id is correct for 5');
        $this->assertSame(
            $events[5]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 5'
        );
        $this->assertSame(
            $events[5]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 5'
        );
        $this->assertEquals(
            array_column($events[5]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 5'
        );
        $this->assertEquals(
            array_column($events[5]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 5'
        );
        $this->assertSame(
            $events[5]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 5'
        );
        $this->assertSame(
            $events[5]['sessionTypeId'],
            $sessionTypes[0]['id'],
            'session type id is correct for event 5'
        );
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[5],
            'instructional notes is correct for event 5'
        );
        $this->assertSame(count($events[5]['learningMaterials']), 0, 'Event 5 has no learning materials');

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
        $this->assertEmpty($events[5]['postrequisites']);
        $this->assertEmpty($events[5]['prerequisites']);
        $this->assertCount(3, $events[5]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::LEARNER, $events[5]['userContexts'][0]);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[5]['userContexts'][1]);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[5]['userContexts'][2]);

        $this->assertSame($events[6]['ilmSession'], 2, 'ilmSession is correct for event 6');
        $this->assertSame($events[6]['startDate'], $ilmSessions[1]['dueDate'], 'dueDate is correct for event 6');
        $this->assertArrayNotHasKey('url', $events[6]);
        $this->assertSame($events[6]['courseTitle'], $courses[1]['title'], 'title is correct for event 6');
        $this->assertSame($events[6]['course'], $courses[1]['id'], 'id is correct for event 6');
        $this->assertSame(
            $events[6]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 6'
        );
        $this->assertSame(
            $events[6]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 6'
        );
        $this->assertEquals(
            array_column($events[6]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 6'
        );
        $this->assertEquals(
            array_column($events[6]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 6'
        );
        $this->assertSame(
            $events[6]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 6'
        );
        $this->assertSame(
            $events[6]['sessionTypeId'],
            $sessionTypes[0]['id'],
            'session type id is correct for event 6'
        );
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[6],
            'instructional notes is correct for event 6'
        );
        $this->assertSame(count($events[6]['learningMaterials']), 0, 'Event 6 has no learning materials');
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
        $this->assertEmpty($events[6]['postrequisites']);
        $this->assertEmpty($events[6]['prerequisites']);
        $this->assertCount(2, $events[6]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[6]['userContexts'][0]);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[6]['userContexts'][1]);

        $this->assertSame($events[7]['ilmSession'], 3, 'ilmSession is correct for event 7');
        $this->assertSame($events[7]['startDate'], $ilmSessions[2]['dueDate'], 'dueDate is correct for event 7');
        $this->assertSame($events[7]['courseTitle'], $courses[1]['title'], 'title is correct for event 7');
        $this->assertArrayNotHasKey('url', $events[7]);
        $this->assertSame($events[7]['course'], $courses[1]['id'], 'id is correct for event 7');
        $this->assertSame(
            $events[7]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 7'
        );
        $this->assertSame(
            $events[7]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 7'
        );
        $this->assertEquals(
            array_column($events[7]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 7'
        );
        $this->assertEquals(
            array_column($events[7]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 7'
        );
        $this->assertSame(
            $events[7]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 7'
        );
        $this->assertSame(
            $events[7]['sessionTypeId'],
            $sessionTypes[0]['id'],
            'session type id is correct for event 7'
        );
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[7],
            'instructional notes is correct for event 7'
        );
        $this->assertSame(count($events[7]['learningMaterials']), 0, 'Event 7 has no learning materials');
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
        $this->assertEmpty($events[7]['postrequisites']);
        $this->assertEmpty($events[7]['prerequisites']);
        $this->assertCount(2, $events[7]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[7]['userContexts'][0]);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[7]['userContexts'][1]);

        $this->assertSame($events[8]['ilmSession'], 4, 'ilmSession is correct for event 8');
        $this->assertSame($events[8]['startDate'], $ilmSessions[3]['dueDate'], 'dueDate is correct for event 8');
        $this->assertArrayNotHasKey('url', $events[8]);
        $this->assertSame($events[8]['courseTitle'], $courses[1]['title'], 'title is correct for event 8');
        $this->assertSame($events[8]['course'], $courses[1]['id'], 'id is correct for event 8');
        $this->assertSame(
            $events[8]['courseExternalId'],
            $courses[1]['externalId'],
            'course external id correct for event 8'
        );
        $this->assertSame(
            $events[8]['courseLevel'],
            $courses[1]['level'],
            'course level correct for event 8'
        );
        $this->assertEquals(
            array_column($events[8]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 8'
        );
        $this->assertEquals(
            array_column($events[8]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 8'
        );
        $this->assertSame(
            $events[8]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 8'
        );
        $this->assertSame(
            $events[8]['sessionTypeId'],
            $sessionTypes[0]['id'],
            'session type id is correct for event 8'
        );
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[8],
            'instructional notes is correct for event 8'
        );
        $this->assertSame(count($events[8]['learningMaterials']), 0, 'Event 8 has no learning materials');
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
        $this->assertEmpty($events[8]['postrequisites']);
        $this->assertEmpty($events[8]['prerequisites']);
        $this->assertCount(2, $events[8]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::LEARNER, $events[8]['userContexts'][0]);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[8]['userContexts'][1]);

        $this->assertSame(
            $events[9]['url'],
            $offerings[0]['url'],
            'url is correct for event 9'
        );
        $this->assertSame($events[9]['startDate'], $offerings[0]['startDate'], 'startDate is correct for event 9');
        $this->assertSame($events[9]['endDate'], $offerings[0]['endDate'], 'endDate is correct for event 9');
        $this->assertSame(
            $events[9]['sessionTypeTitle'],
            $sessionTypes[0]['title'],
            'session type title is correct for event 9'
        );
        $this->assertSame(
            $events[9]['sessionTypeId'],
            $sessionTypes[0]['id'],
            'session type id is correct for event 9'
        );
        $this->assertSame(
            $events[9]['course'],
            $courses[0]['id'],
            'course id correct for event 9'
        );
        $this->assertSame(
            $events[9]['courseExternalId'],
            $courses[0]['externalId'],
            'course external id correct for event 9'
        );
        $this->assertSame(
            $events[9]['courseLevel'],
            $courses[0]['level'],
            'course level correct for event 9'
        );
        $this->assertEquals(
            array_column($events[9]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 9'
        );
        $this->assertEquals(
            array_column($events[9]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 9'
        );
        $this->assertSame(
            $events[9]['sessionDescription'],
            $sessions[0]['description'],
            'session description is correct for event 9'
        );
        $this->assertSame(
            $events[9]['instructionalNotes'],
            $sessions[0]['instructionalNotes'],
            'instructional notes is correct for event 9'
        );
        $this->assertEquals(
            array_column($events[9]['sessionTerms'], 'id'),
            $sessions[0]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertSame(
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
        $this->assertSame(0, count($events[9]['postrequisites']));
        $this->assertEmpty($events[9]['prerequisites']);
        $this->assertCount(2, $events[9]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::LEARNER, $events[9]['userContexts'][0]);
        $this->assertEquals(CalendarEventUserContext::INSTRUCTOR, $events[9]['userContexts'][1]);

        /** @var OfferingInterface $offering */
        $offering = $this->fixtures->getReference('offerings8', Offering::class);
        $this->assertSame(8, $events[10]['offering'], 'offering is correct for event 10');
        $this->assertSame(
            $events[10]['startDate'],
            $offering->getStartDate()->format('c'),
            'startDate is correct for event 10'
        );
        $this->assertSame(
            $events[10]['endDate'],
            $offering->getEndDate()->format('c'),
            'endDate is correct for event 10'
        );
        $this->assertArrayNotHasKey('url', $events[10]);
        $this->assertSame(
            $events[10]['sessionTypeTitle'],
            $sessionTypes[1]['title'],
            'session type title is correct for event 10'
        );
        $this->assertSame(
            $events[10]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 10'
        );
        $this->assertSame(
            $events[10]['course'],
            $courses[1]['id'],
            'course id correct for event 10'
        );
        $this->assertSame(
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
        $this->assertEmpty($events[10]['postrequisites']);
        $this->assertSame(3, count($events[10]['prerequisites']));
        $sessionIds = array_unique(array_column($events[10]['prerequisites'], 'session'));
        $this->assertCount(1, $events[10]['userContexts']);
        $this->assertEquals(CalendarEventUserContext::COURSE_DIRECTOR, $events[10]['userContexts'][0]);

        sort($sessionIds);
        $this->assertSame([2], $sessionIds);
        foreach ($events as $event) {
            $this->assertSame($userId, $event['user']);
        }
    }

    public function testMultidayEvent(): void
    {
        $offerings = self::getContainer()->get(OfferingData::class)->getAll();
        $userId = 2;
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');

        $events = $this->getEvents(
            $userId,
            $from->getTimestamp(),
            $to->getTimestamp(),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $this->assertSame(1, count($events), 'Expected events returned');

        $this->assertSame($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertSame($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertSame($events[0]['offering'], $offerings[5]['id']);
    }

    public function testPrivilegedUsersGetsEventsForUnpublishedSessions(): void
    {
        $userId = 2;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );
        $event = $events[3];
        $this->assertFalse($event['isPublished']);
        $this->assertFalse($event['isScheduled']);
        $lms = $event['learningMaterials'];

        $this->assertSame(7, count($lms));
        $this->assertSame('2', $lms[0]['sessionLearningMaterial']);
        $this->assertSame('3', $lms[1]['sessionLearningMaterial']);
        $this->assertSame('4', $lms[2]['sessionLearningMaterial']);
        $this->assertSame('5', $lms[3]['sessionLearningMaterial']);
        $this->assertSame('6', $lms[4]['sessionLearningMaterial']);
        $this->assertSame('7', $lms[5]['sessionLearningMaterial']);
        $this->assertSame('8', $lms[6]['sessionLearningMaterial']);
    }

    public function testGetEventsBySessionForCourseDirector(): void
    {
        $userId = 2;
        $sessionId = 3;

        $events = $this->getEventsForSessionId(
            $userId,
            $sessionId,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );

        $this->assertSame(3, count($events), 'Expected events returned');
        $this->assertSame(
            $sessionId,
            $events[0]['session']
        );
        $this->assertSame(6, $events[0]['offering']);
        $this->assertSame($sessionId, $events[0]['session']);
        $this->assertSame(7, $events[1]['offering']);
        $this->assertSame($sessionId, $events[1]['session']);
        $this->assertSame(8, $events[2]['offering']);
        $this->assertSame($sessionId, $events[2]['session']);
        foreach ($events as $event) {
            $this->assertContains(CalendarEventUserContext::COURSE_DIRECTOR, $event['userContexts']);
        }
    }

    public function testGetEventsBySessionForLearner(): void
    {
        $userId = 5;
        $sessionId = 1;

        $events = $this->getEventsForSessionId(
            $userId,
            $sessionId,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );

        $this->assertSame(2, count($events), 'Expected events returned');
        $this->assertSame(
            $sessionId,
            $events[0]['session']
        );
        $this->assertSame(1, $events[0]['offering']);
        $this->assertSame($sessionId, $events[0]['session']);
        $this->assertSame(2, $events[1]['offering']);
        $this->assertSame($sessionId, $events[1]['session']);
        foreach ($events as $event) {
            $this->assertContains(CalendarEventUserContext::LEARNER, $event['userContexts']);
        }
    }

    public function testAttachedInstructorsUseDisplayNameAndPronouns(): void
    {
        $userId = 2;
        $events = $this->getEvents(
            $userId,
            0,
            100000000000,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );
        $users = self::getContainer()->get(UserData::class)->getAll();

        $this->assertSame($events[0]['offering'], 3);

        $this->assertSame(2, count($events[0]['instructors']));
        $this->assertSame("{$users[1]['displayName']} ({$users[1]['pronouns']})", $events[0]['instructors'][0]);
        $this->assertSame($users[3]['displayName'], $events[0]['instructors'][1]);
    }

    public function testMissingFrom(): void
    {
        $userId = 5;
        $parameters = [
            'version' => $this->apiVersion,
            'id' => $userId,
            'to' => 1000,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_userevent_getevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testMissingTo(): void
    {
        $userId = 5;
        $parameters = [
            'version' => $this->apiVersion,
            'id' => $userId,
            'from' => 1000,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_userevent_getevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->createJwtFromUserId($this->kernelBrowser, $userId)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }

    public function testAccessDenied(): void
    {
        $this->runAccessDeniedTest();
    }

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $this->runAccessDeniedTest($jwt, Response::HTTP_FORBIDDEN);
    }

    protected function getEvents(int $userId, int $from, int $to, ?string $jwt = null): array
    {
        $parameters = [
            'version' => $this->apiVersion,
            'id' => $userId,
            'from' => $from,
            'to' => $to,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_userevent_getevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)['userEvents'];
    }

    protected function getEventsForSessionId(int $userId, int $sessionId, ?string $jwt = null): array
    {
        $parameters = [
            'version' => $this->apiVersion,
            'id' => $userId,
            'session' => $sessionId,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_userevent_getevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)['userEvents'];
    }

    protected function runAccessDeniedTest(
        ?string $jwt = null,
        int $expectedResponseCode = Response::HTTP_UNAUTHORIZED
    ): void {
        $parameters = [
            'version' => $this->apiVersion,
            'from' => 1000000,
            'to' => 1000000,
            'id' => 99,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_userevent_getevents',
            $parameters
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, $expectedResponseCode);
    }
}
