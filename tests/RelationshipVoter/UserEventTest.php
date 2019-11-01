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
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
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
        $entity->school = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->course = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([$entity->course]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([$entity->course]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([$entity->session]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([$entity->offering]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->ilmSession = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedIlmIds')->andReturn([$entity->ilmSession]);

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
        $entity->school = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([$entity->school]);
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
        $entity->school = 1;
        $entity->course = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([$entity->course]);
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
        $entity->school = 1;
        $entity->course = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([$entity->course]);
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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([$entity->session]);
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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([$entity->offering]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->ilmSession = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedIlmIds')->andReturn([$entity->ilmSession]);

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
        $entity->school = 1;
        $entity->course = 1;
        $entity->session = 1;
        $entity->isPublished = true;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedIlmIds')->andReturn([]);
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
        $entity->school = 1;
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([$entity->school]);
        $sessionUser->shouldReceive('getId')->andReturn($userId);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
