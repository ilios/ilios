<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Objective as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\ObjectiveInterface;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ObjectiveTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(ObjectiveInterface::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanCreateProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanEditProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteProgramYearObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $programYear = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection([$programYear]));
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $programYear->shouldReceive('getProgram')->andReturn($program);
        $programYear->shouldReceive('getId')->andReturn(1);
        $program->shouldReceive('getSchool')->andReturn($school);
        $program->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreateCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanEditCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteCourseObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection([$course]));
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection());
        $course->shouldReceive('getSchool')->andReturn($school);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreateSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanEditSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteSessionObjective()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Objective::class);
        $session = m::mock(Session::class);
        $course = m::mock(Course::class);
        $school = m::mock(School::class);
        $entity->shouldReceive('getProgramYears')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getCourses')->andReturn(new ArrayCollection());
        $entity->shouldReceive('getSessions')->andReturn(new ArrayCollection([$session]));
        $session->shouldReceive('getCourse')->andReturn($course);
        $course->shouldReceive('getSchool')->andReturn($school);
        $session->shouldReceive('getId')->andReturn(1);
        $course->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getId')->andReturn(1);
        $this->permissionChecker->shouldReceive('canUpdateSession')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create allowed");
    }
}
