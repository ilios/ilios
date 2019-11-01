<?php

namespace App\Tests\RelationshipVoter;

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
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([$entity->school]);

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
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([$entity->school]);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewUnpublishedEventsIfCurrentUserIsProgramDirectorInEventowningSchool()
    {
        $schoolId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        /* @var SchoolEvent $entity */
        $entity = m::mock(SchoolEvent::class);
        $sessionUser = $token->getUser();

        $entity->school = $schoolId;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([$entity->school]);

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
        $entity->course = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([$entity->course]);

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
        $entity->course = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([$entity->course]);

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
        $entity->course = 1;
        $entity->session = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([$entity->session]);

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
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([$entity->offering]);

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
        $entity->course = 1;
        $entity->session = 1;
        $entity->offering = 1;
        $entity->ilmSession = 1;

        $entity->isPublished = false;
        $sessionUser->shouldReceive('getSchoolId')->andReturn($entity->school);
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedIlmIds')->andReturn([$entity->ilmSession]);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }


    public function testCanNotViewUnpublishedSchoolEventsIfNotTeachingAdministratingOrDirectingEvents()
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
        $sessionUser->shouldReceive('getAdministeredSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedProgramSchoolIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getDirectedCourseIds')->andReturn([]);
        $sessionUser->shouldReceive('getAdministeredSessionIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedOfferingIds')->andReturn([]);
        $sessionUser->shouldReceive('getInstructedIlmIds')->andReturn([]);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
