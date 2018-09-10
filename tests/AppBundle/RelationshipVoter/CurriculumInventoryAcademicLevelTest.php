<?php
namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\CurriculumInventoryAcademicLevel as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\CurriculumInventoryAcademicLevel;
use AppBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurriculumInventoryAcademicLevelTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(CurriculumInventoryAcademicLevel::class), [AbstractVoter::VIEW]);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryAcademicLevel::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }
}
