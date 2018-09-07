<?php
namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\Cohort as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\Cohort;
use AppBundle\Entity\ProgramYear;
use AppBundle\Entity\ProgramYearInterface;
use AppBundle\Service\Config;
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
        $this->checkRootEntityAccess(m::mock(Cohort::class), [AbstractVoter::VIEW, AbstractVoter::EDIT]);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getProgramYear')->andReturn(m::mock(ProgramYearInterface::class));
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Cohort::class);
        $entity->shouldReceive('getProgramYear')->andReturn(m::mock(ProgramYearInterface::class));
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }
}
