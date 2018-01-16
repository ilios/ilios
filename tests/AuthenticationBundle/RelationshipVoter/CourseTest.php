<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Course as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CourseTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(Course::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanUnlock()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $this->permissionChecker->shouldReceive('canUnlockCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNLOCK]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotUnlock()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $this->permissionChecker->shouldReceive('canUnlockCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNLOCK]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanUnarchive()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $this->permissionChecker->shouldReceive('canUnarchiveCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNARCHIVE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotUnarchive()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Course::class);
        $this->permissionChecker->shouldReceive('canUnarchiveCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNARCHIVE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }
}
