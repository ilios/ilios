<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\CalendarEvent;
use App\Classes\UserEvent;
use App\Classes\UserMaterial;
use App\Entity\LearningMaterialStatusInterface;
use App\Tests\TestCase;
use DateTime;

/**
 * Class UserEventTest
 * @package App\Tests\Classes
 * @covers \App\Classes\CalendarEvent
 * @covers \App\Classes\UserEvent
 */
class UserEventTest extends TestCase
{
    protected UserEvent $userEvent;

    protected function setUp(): void
    {
        $this->userEvent = new UserEvent();
    }

    protected function tearDown(): void
    {
        unset($this->userEvent);
    }

    /**
     * @covers \App\Classes\CalendarEvent::removeMaterialsInDraft
     */
    public function testRemoveMaterialsInDraft(): void
    {
        $draftMaterial = new UserMaterial();
        $draftMaterial->status = LearningMaterialStatusInterface::IN_DRAFT;
        $revisedMaterial = new UserMaterial();
        $revisedMaterial->status = LearningMaterialStatusInterface::REVISED;
        $finalizedMaterial = new UserMaterial();
        $finalizedMaterial->status = LearningMaterialStatusInterface::FINALIZED;

        $this->userEvent->learningMaterials = [ $draftMaterial, $revisedMaterial, $finalizedMaterial ];
        $this->userEvent->isPublished = true;
        $this->userEvent->clearDataForUnprivilegedUsers(new DateTime());
        $this->assertEquals(2, count($this->userEvent->learningMaterials));
        $this->assertTrue(in_array($finalizedMaterial, $this->userEvent->learningMaterials));
        $this->assertTrue(in_array($revisedMaterial, $this->userEvent->learningMaterials));
    }

    /**
     * @covers \App\Classes\UserEvent::createFromCalendarEvent
     */
    public function testCreateFromCalendarEvent(): void
    {
        $userId = 100;
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

        $userEvent = UserEvent::createFromCalendarEvent($userId, $calendarEvent);
        $this->assertSame($userId, $userEvent->user);
        $this->assertSame($calendarEvent->attendanceRequired, $userEvent->attendanceRequired);
        $this->assertSame($calendarEvent->attireRequired, $userEvent->attireRequired);
        $this->assertSame($calendarEvent->color, $userEvent->color);
        $this->assertSame($calendarEvent->courseExternalId, $userEvent->courseExternalId);
        $this->assertSame($calendarEvent->courseTitle, $userEvent->courseTitle);
        $this->assertSame($calendarEvent->course, $userEvent->course);
        $this->assertSame($calendarEvent->endDate, $userEvent->endDate);
        $this->assertSame($calendarEvent->equipmentRequired, $userEvent->equipmentRequired);
        $this->assertSame($calendarEvent->instructionalNotes, $userEvent->instructionalNotes);
        $this->assertSame($calendarEvent->startDate, $userEvent->startDate);
        $this->assertSame($calendarEvent->sessionDescription, $userEvent->sessionDescription);
        $this->assertSame($calendarEvent->userContexts, $userEvent->userContexts);
    }
}
