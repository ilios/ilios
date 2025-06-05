<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\UserInterface;
use App\Entity\UserSessionMaterialStatusInterface;
use App\RelationshipVoter\UserSessionMaterialStatus as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class UserSessionMaterialStatusTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testSelfHasFullAccess(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $user = m::mock(UserInterface::class);
        $entity = m::mock(UserSessionMaterialStatusInterface::class);
        foreach ([VoterPermissions::VIEW, VoterPermissions::DELETE, VoterPermissions::EDIT] as $attr) {
            $sessionUser->shouldReceive('getUser')->andReturn($user);
            $entity->shouldReceive('getUser')->andReturn($user);
            $sessionUser->shouldReceive('isTheUser')->with($user)->andReturn(true);
            $response = $this->voter->vote($token, $entity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "{$attr} granted");
        }
    }

    public function testDenyOtherUserAccess(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $user = m::mock(UserInterface::class);
        $user2 = m::mock(UserInterface::class);
        $entity = m::mock(UserSessionMaterialStatusInterface::class);
        foreach (
            [
            VoterPermissions::VIEW,
            VoterPermissions::DELETE,
            VoterPermissions::CREATE,
            VoterPermissions::EDIT,
            ] as $attr
        ) {
            $sessionUser->shouldReceive('getUser')->andReturn($user);
            $entity->shouldReceive('getUser')->andReturn($user2);
            $sessionUser->shouldReceive('isTheUser')->with($user2)->andReturn(false);
            $response = $this->voter->vote($token, $entity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "{$attr} grandeniedted");
        }
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [UserSessionMaterialStatusInterface::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, true],
            [VoterPermissions::DELETE, true],
            [VoterPermissions::EDIT, true],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
