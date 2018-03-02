<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\ProgramYear as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ProgramYearTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $config = m::mock(Config::class);
        $config->shouldReceive('useNewPermissionsSystem')->andReturn(true);
        $this->voter = new Voter($this->permissionChecker, $config);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(ProgramYear::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canDeleteProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canDeleteProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $this->permissionChecker->shouldReceive('canCreateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $program = m::mock(Program::class);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $this->permissionChecker->shouldReceive('canCreateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanUnlock()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUnlockProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNLOCK]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotUnlock()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUnlockProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNLOCK]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanUnarchive()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUnarchiveProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNARCHIVE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotUnarchive()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYear::class);
        $this->permissionChecker->shouldReceive('canUnarchiveProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::UNARCHIVE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }
}
