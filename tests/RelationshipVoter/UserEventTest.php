<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\UserEvent;
use App\Classes\VoterPermissions;
use App\RelationshipVoter\UserEvent as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class UserEventTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess(): void
    {
        $this->checkRootEntityAccess(m::mock(UserEvent::class), [VoterPermissions::VIEW]);
    }

    public function testCanViewOwnPublishedEvents(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->isPublished = true;
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSchoolAdministratorInOwningSchool(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSchoolDirectorInOwningSchool(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsDirectingProgramLinkedToOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsCourseAdministratorInOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsCourseDirectorInOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSessionAdministratorInOwningSession(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsDirectingProgramLinkedToOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherUnpublishedEventsIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherUnpublishedEventsIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherUnpublishedEventsIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherUnpublishedEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherUnpublishedEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
        $entity->isPublished = false;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewOtherUsersPublishedEventsIfCurrentUserIsNotAdminDirectorOrInstructorToEvent(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
        $entity->isPublished = true;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $user->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $user->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);
        $user->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsDirectingProgramLinkedToOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
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

    public function testCanViewDraftDataOnOwnUserEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
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

    public function testCanNotViewDraftDataOnOwnUserEventsIfCurrentUserIsNotAdminDirectorOrInstructorToEvent(): void
    {
        $userId = 1;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $userId;
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

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsSchoolAdministratorInOwningSchool(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsSchoolDirectorInOwningSchool(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsDirectingProgramLinkedToOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsCourseAdministratorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsCourseDirectorInOwningCourse(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsSessionAdministratorInOwningSession(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $user->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isDirectingProgramLinkedToCourse')->with($entity->school)->andReturn(false);
        $user->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $user->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);

        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW_DRAFT_CONTENTS]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsInstructorInOwningOffering(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
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

    public function testCanViewDraftDataOnOtherUsersEventsIfCurrentUserIsInstructorInOwningIlm(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
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

    public function testCanNotViewDraftDataOnOtherUserEventsIfCurrentUserIsNotAdminDirectorOrInstructorToEvent(): void
    {
        $userId = 1;
        $otherUserId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(UserEvent::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
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

    public static function supportsTypeProvider(): array
    {
        return [
            [UserEvent::class, true],
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
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
