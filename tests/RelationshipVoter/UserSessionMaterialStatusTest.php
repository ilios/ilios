<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Entity\UserInterface;
use App\Entity\UserSessionMaterialStatusInterface;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\UserSessionMaterialStatus as Voter;
use App\Service\PermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserSessionMaterialStatusTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testSelfHasFullAccess()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $sessionUser = $token->getUser();
        $user = m::mock(UserInterface::class);
        $entity = m::mock(UserSessionMaterialStatusInterface::class);
        foreach ([AbstractVoter::VIEW, AbstractVoter::DELETE, AbstractVoter::EDIT] as $attr) {
            $sessionUser->shouldReceive('getUser')->andReturn($user);
            $entity->shouldReceive('getUser')->andReturn($user);
            $sessionUser->shouldReceive('isTheUser')->with($user)->andReturn(true);
            $response = $this->voter->vote($token, $entity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${attr} granted");
        }
    }

    public function testDenyOtherUserAccess()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $sessionUser = $token->getUser();
        $user = m::mock(UserInterface::class);
        $user2 = m::mock(UserInterface::class);
        $entity = m::mock(UserSessionMaterialStatusInterface::class);
        foreach ([AbstractVoter::VIEW, AbstractVoter::DELETE, AbstractVoter::CREATE, AbstractVoter::EDIT] as $attr) {
            $sessionUser->shouldReceive('getUser')->andReturn($user);
            $entity->shouldReceive('getUser')->andReturn($user2);
            $sessionUser->shouldReceive('isTheUser')->with($user2)->andReturn(false);
            $response = $this->voter->vote($token, $entity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${attr} grandeniedted");
        }
    }
}
