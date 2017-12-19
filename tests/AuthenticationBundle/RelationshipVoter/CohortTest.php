<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Cohort as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\DTO\CohortDTO;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CohortTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(Cohort::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCohort')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCohort')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCohort')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCohort')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCohort')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCohort')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCohort')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCohort')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
