<?php
namespace App\Tests\Classes;

use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SchoolEventTest
 * @package App\Tests\Classes
 * @covers \App\Classes\SchoolEvent
 */
class SchoolEventTest extends TestCase
{
    /**
     * @covers SchoolEvent::createFromCalendarEvent
     */
    public function testCreateFromCalendarEvent()
    {
        $schoolId = 100;
        $calendarEvent = new CalendarEvent();
        $calendarEvent->attendanceRequired = false;
        $calendarEvent->attireRequired = true;
        $calendarEvent->color = '#fff';
        $calendarEvent->courseExternalId = 12;
        $calendarEvent->course = 17;
        $calendarEvent->courseTitle = 'Test Event';
        $calendarEvent->endDate = new \DateTime();
        $calendarEvent->equipmentRequired = true;
        $calendarEvent->startDate = new \DateTime();
        $calendarEvent->instructionalNotes = 'lorem ipsum';
        $calendarEvent->sessionDescription = 'something';

        $schoolEvent = SchoolEvent::createFromCalendarEvent($schoolId, $calendarEvent);
        $this->assertSame($schoolId, $schoolEvent->school);
        $this->assertSame($calendarEvent->attendanceRequired, $schoolEvent->attendanceRequired);
        $this->assertSame($calendarEvent->attireRequired, $schoolEvent->attireRequired);
        $this->assertSame($calendarEvent->color, $schoolEvent->color);
        $this->assertSame($calendarEvent->courseExternalId, $schoolEvent->courseExternalId);
        $this->assertSame($calendarEvent->courseTitle, $schoolEvent->courseTitle);
        $this->assertSame($calendarEvent->course, $schoolEvent->course);
        $this->assertSame($calendarEvent->endDate, $schoolEvent->endDate);
        $this->assertSame($calendarEvent->equipmentRequired, $schoolEvent->equipmentRequired);
        $this->assertSame($calendarEvent->instructionalNotes, $schoolEvent->instructionalNotes);
        $this->assertSame($calendarEvent->startDate, $schoolEvent->startDate);
        $this->assertSame($calendarEvent->sessionDescription, $schoolEvent->sessionDescription);
    }
}
