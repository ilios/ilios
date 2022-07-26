<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\SessionLearningMaterial as Voter;
use App\Service\PermissionChecker;
use App\Entity\Course;
use App\Entity\SessionLearningMaterial;
use App\Entity\Session;
use App\Entity\School;
use App\Service\Config;
use DateTime;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SessionLearningMaterialTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(SessionLearningMaterial::class));
    }

    public function testCanViewUserPerformingNonLearnerFunction()
    {
        $token = $this->createMockTokenWithSessionUserPerformingNonLearnerFunction();
        $entity = m::mock(SessionLearningMaterial::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerOnly()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerBeforeStartDate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(new DateTime('tomorrow'));
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerAfterEndDate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(new DateTime('yesterday'));
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanViewAsLearnerInSession()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUser->shouldReceive('isLearnerInSession')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(13);
        $entity->shouldReceive('getStartDate')->andReturn(null);
        $entity->shouldReceive('getEndDate')->andReturn(null);
        $entity->shouldReceive('getSession')->andReturn($session);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(SessionLearningMaterial::class);
        $session = m::mock(Session::class);
        $session->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
