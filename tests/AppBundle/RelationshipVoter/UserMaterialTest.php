<?php

namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\UserMaterial as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Classes\UserMaterial;
use AppBundle\Entity\LearningMaterialStatusInterface;
use AppBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserMaterialTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(UserMaterial::class), [AbstractVoter::VIEW]);
    }

    public function testCanViewNonDraftMaterials()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserMaterial::class);
        $sessionUser = $token->getUser();

        $entity->status = LearningMaterialStatusInterface::FINALIZED;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanViewDraftMaterialsIfUserPerformsNonStudentFunction()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserMaterial::class);
        $sessionUser = $token->getUser();

        $entity->status = LearningMaterialStatusInterface::IN_DRAFT;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotViewDraftMaterials()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(UserMaterial::class);
        $sessionUser = $token->getUser();

        $entity->status = LearningMaterialStatusInterface::IN_DRAFT;
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);

        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
