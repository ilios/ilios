<?php

namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\TemporaryFileSystem as Voter;
use App\Service\PermissionChecker;
use App\Service\Config;
use App\Service\TemporaryFileSystem;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TemporaryFileSystemTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(TemporaryFileSystem::class), [AbstractVoter::CREATE]);
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
