<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Session as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\DTO\SessionDTO;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SessionTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootAccess(Session::class, SessionDTO::class);
    }

    public function testCanViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(SessionDTO::class);
        $dto->id = 1;
        $dto->course = 1;
        $dto->school = 1;
        $this->permissionChecker->shouldReceive('canReadSession')->andReturn(true);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testCanNotViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(SessionDTO::class);
        $dto->id = 1;
        $dto->course = 1;
        $dto->school = 1;
        $this->permissionChecker->shouldReceive('canReadSession')->andReturn(false);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " View allowed");
    }

    public function testCanNotView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Edit allowed");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Delete allowed");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Session::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Create allowed");
    }
}
