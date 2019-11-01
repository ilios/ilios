<?php

namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\UserEvent as Voter;
use App\Service\PermissionChecker;
use App\Classes\UserEvent;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserEventTest extends AbstractBase
{
    public function setup()
    {
        /* @var PermissionChecker permissionChecker */
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(UserEvent::class), [AbstractVoter::VIEW]);
    }

    public function testCanViewOwnPublishedEvents()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSchoolAdministratorInEventowningSchool()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSchoolDirectorInEventowningSchool()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsProgramDirectorInEventowningSchool()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsCourseAdministratorInEventowningCourse()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsCourseDirectorInEventowningCourse()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfUserIsSessionAdministratorInEventowningSession()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfCurrentUserIsInstructorInEventowningOffering()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOwnUnpublishedEventsIfCurrentUserIsInstructorInEventowningIlm()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $userId;
        $entity->school = 2;
        $entity->course = 3;
        $entity->session = 4;
        $entity->offering = 5;
        $entity->ilmSession = 6;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSchoolAdministratorInEventowningSchool()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSchoolDirectorInEventowningSchool()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsProgramDirectorInEventowningSchool()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsCourseAdministratorInEventowningCourse()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsCourseDirectorInEventowningCourse()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsSessionAdministratorInEventowningSession()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsInstructorInEventowningOffering()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewOtherPublishedEventsIfCurrentUserIsInstructorInEventowningIlm()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewOtherUsersPublishedEventsIfNotTeachingAdministratingOrDirectingEvents()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->school = 3;
        $entity->course = 4;
        $entity->session = 5;
        $entity->offering = 6;
        $entity->ilmSession = 7;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingProgramInSchool')->with($entity->school)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isDirectingCourse')->with($entity->course)->andReturn(false);
        $sessionUser->shouldReceive('isAdministeringSession')->with($entity->session)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingOffering')->with($entity->offering)->andReturn(false);
        $sessionUser->shouldReceive('isInstructingIlm')->with($entity->ilmSession)->andReturn(false);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanNotViewOtherUsersUnpublishedEvents()
    {
        $userId = 1;
        $otherUserId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var UserEvent $entity */
        $entity = m::mock(UserEvent::class);
        $sessionUser = $token->getUser();

        $entity->user = $otherUserId;
        $entity->isPublished = false;
        $entity->school = 3;
        $sessionUser->shouldReceive('isAdministeringSchool')->with($entity->school)->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
