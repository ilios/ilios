<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SchoolEvent;
use App\Classes\VoterPermissions;
use App\RelationshipVoter\SchoolEvent as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class SchoolEventTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess(): void
    {
        $this->checkRootEntityAccess(m::mock(SchoolEvent::class), [VoterPermissions::VIEW]);
    }

    public function testCanViewPublishedSchoolEventInCurrentUsersPrimarySchool(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->isPublished = true;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserisDirectingProgramLinkedToOwningCourse(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->offering = 4;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 2;
        $entity->session = 3;
        $entity->offering = 4;
        $entity->ilmSession = 5;

        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }


    public function testCanNotViewUnpublishedSchoolEventsIfCurrentUserIsNotAdminDirectorOrInstructorToEvent(): void
    {
        $schoolId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);

        $entity->school = $schoolId;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->ilmSession = 1;
        $entity->isPublished = false;
        $user->shouldReceive('getSchoolId')->andReturn($entity->school);
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanViewDraftDataIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserisDirectingProgramLinkedToOwningCoursel(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewDraftDataIfCurrentUserIsNotAdministratorDirectorOrInstructorToEvent(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserisDirectingProgramLinkedToOwningCourse(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsIlmLearnerInEvent(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('isLearnerInIlm')->with($entity->ilmSession)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewVirtualLinkIfCurrentUserIsOfferingLearnerInEvent(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('isLearnerInIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('isLearnerInOffering')->with($entity->offering)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewVirtualLinkIfCurrentUserIsNotAssociatedWithEvent(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(SchoolEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;

        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('isLearnerInIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('isLearnerInOffering')->with($entity->offering)->andReturn(false);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_VIRTUAL_LINK]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [SchoolEvent::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, false],
            [VoterPermissions::DELETE, false],
            [VoterPermissions::EDIT, false],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, true],
            [VoterPermissions::VIEW_VIRTUAL_LINK, true],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
