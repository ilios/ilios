<?php
namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\CourseLearningMaterial as Voter;
use App\Service\PermissionChecker;
use App\Entity\Course;
use App\Entity\CourseLearningMaterial;
use App\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CourseLearningMaterialTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(CourseLearningMaterial::class));
    }

    public function testCanViewUserPerformingNonLearnerFunction()
    {
        $token = $this->createMockTokenWithSessionUserPerformingNonLearnerFunction();
        $entity = m::mock(CourseLearningMaterial::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewAsLearnerOnly()
    {
        $token = $this->createMockTokenWithSessionUserPerformingOnlyLearnerFunction();
        $entity = m::mock(CourseLearningMaterial::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $school->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CourseLearningMaterial::class);
        $course = m::mock(Course::class);
        $course->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
