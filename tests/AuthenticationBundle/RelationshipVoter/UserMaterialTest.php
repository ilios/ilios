<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\UserMaterial as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Service\Config;
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
