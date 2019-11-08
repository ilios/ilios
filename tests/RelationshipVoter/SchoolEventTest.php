<?php

namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractCalendarEvent;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\SchoolEvent as Voter;
use App\Service\PermissionChecker;
use App\Classes\SchoolEvent;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SchoolEventTest extends AbstractBase
{
    public function setup()
    {
        /* @var PermissionChecker permissionChecker */
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(SchoolEvent::class), [AbstractVoter::VIEW]);
    }

    public function testCanViewPublishedSchoolEventInCurrentUsersPrimarySchool()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSchoolAdministratorInEventowningSchool()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSchoolDirectorInEventowningSchool()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserisDirectingProgramLinkedToEventowningCoursel()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsCourseAdministratorInEventowningCourse()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsCourseDirectorInEventowningCourse()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSessionAdministratorInEventowningSession()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsInstructorInEventowningOffering()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->offering = 4;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsInstructorInEventowningIlm()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->offering = 4;
        $entity->ilmSession = 5;

        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }


    public function testCanNotViewUnpublishedSchoolEventsIfCurrentUserIsNotAdministratorDirectorOrInstructorToEvent()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->ilmSession = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanViewDraftDataIfCurrentUserIsSchoolAdministratorInEventowningSchool()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsSchoolDirectorInEventowningSchool()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserisDirectingProgramLinkedToEventowningCoursel()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsCourseAdministratorInEventowningCourse()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsCourseDirectorInEventowningCourse()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsSessionAdministratorInEventowningSession()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsInstructorInEventowningOffering()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsInstructorInEventowningIlm()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsNotAdministratorDirectorOrInstructorToEvent()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $sessionUser = $token->getUser();
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);

        $response = $this->voter->vote($token, $entity, [AbstractCalendarEvent::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
