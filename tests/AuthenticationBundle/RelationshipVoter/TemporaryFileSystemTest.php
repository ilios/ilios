<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\TemporaryFileSystem as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;

use Ilios\CoreBundle\Service\TemporaryFileSystem;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TemporaryFileSystemTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker, true);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(TemporaryFileSystem::class, [AbstractVoter::CREATE]);
    }

    public function testCanCreateTemporaryFileSystem()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TemporaryFileSystem::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateTemporaryFileSystem()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TemporaryFileSystem::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
