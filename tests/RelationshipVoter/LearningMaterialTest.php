<?php

namespace Tests\App\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\LearningMaterial as Voter;
use App\Service\PermissionChecker;
use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialInterface;

use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class LearningMaterialTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(LearningMaterialInterface::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanCreateLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function testCanEditLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEditLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDeleteLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDeleteLearningMaterial()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(LearningMaterial::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }
}
