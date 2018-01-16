<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\CurriculumInventoryAcademicLevel as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CurriculumInventoryAcademicLevelTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(CurriculumInventoryAcademicLevel::class, [AbstractVoter::VIEW]);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(CurriculumInventoryAcademicLevel::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }
}
