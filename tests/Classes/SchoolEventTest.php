<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent;
use App\Classes\UserMaterial;
use App\Tests\TestCase;
use Mockery as m;
use DateTime;

/**
 * Class SchoolEventTest
 * @package App\Tests\Classes
 */
#[CoversClass(SchoolEvent::class)]
class SchoolEventTest extends TestCase
{
    protected SchoolEvent $schoolEvent;

    protected function setUp(): void
    {
        $this->schoolEvent = new SchoolEvent();
    }

    protected function tearDown(): void
    {
        unset($this->schoolEvent);
    }

    public function testCreateFromCalendarEvent(): void
    {
        $schoolId = 100;
        $calendarEvent = new CalendarEvent();
        $calendarEvent->attendanceRequired = false;
        $calendarEvent->attireRequired = true;
        $calendarEvent->color = '#fff';
        $calendarEvent->courseExternalId = '12';
        $calendarEvent->course = 17;
        $calendarEvent->courseTitle = 'Test Event';
        $calendarEvent->endDate = new DateTime();
        $calendarEvent->equipmentRequired = true;
        $calendarEvent->startDate = new DateTime();
        $calendarEvent->instructionalNotes = 'lorem ipsum';
        $calendarEvent->sessionDescription = 'something';
        $calendarEvent->school = $schoolId;

        $schoolEvent = SchoolEvent::createFromCalendarEvent($calendarEvent);
        $this->assertSame($calendarEvent->school, $schoolEvent->school);
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

    public function testClearDataForUnprivilegedUsers(): void
    {
        $userMaterial = m::mock(UserMaterial::class);
        $userMaterial->shouldReceive('clearMaterial');
        $this->schoolEvent->isPublished = true;
        $this->schoolEvent->learningMaterials = [ $userMaterial ];
        $this->schoolEvent->clearDataForUnprivilegedUsers();
        $userMaterial->shouldHaveReceived('clearMaterial')->once();
        $this->assertEquals($this->schoolEvent->learningMaterials[0], $userMaterial);
    }

    public function testClearDataForStudentAssociatedWithEvent(): void
    {
        $date = new DateTime();
        $userMaterial = m::mock(UserMaterial::class);
        $userMaterial->shouldReceive('clearTimedMaterial');
        $this->schoolEvent->isPublished = true;
        $this->schoolEvent->learningMaterials = [ $userMaterial ];
        $this->schoolEvent->clearDataForStudentAssociatedWithEvent($date);
        $userMaterial->shouldHaveReceived('clearTimedMaterial', [$date])->once();
        $this->assertEquals($this->schoolEvent->learningMaterials[0], $userMaterial);
    }
}
