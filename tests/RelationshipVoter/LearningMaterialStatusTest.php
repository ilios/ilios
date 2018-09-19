<?php
namespace Tests\App\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\LearningMaterialStatus as Voter;
use App\Service\PermissionChecker;
use App\Entity\LearningMaterialStatus;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class LearningMaterialStatusTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(LearningMaterialStatus::class), [AbstractVoter::VIEW]);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterialStatus::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }
}
