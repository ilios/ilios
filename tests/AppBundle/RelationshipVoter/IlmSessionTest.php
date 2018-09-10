<?php
namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\IlmSession as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\Course;
use AppBundle\Entity\IlmSession;
use AppBundle\Entity\Session;
use AppBundle\Entity\School;
use AppBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IlmSessionTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(IlmSession::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(IlmSession::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(IlmSession::class);
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
        $entity = m::mock(IlmSession::class);
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
        $entity = m::mock(IlmSession::class);
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
        $entity = m::mock(IlmSession::class);
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
        $entity = m::mock(IlmSession::class);
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
        $entity = m::mock(IlmSession::class);
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
