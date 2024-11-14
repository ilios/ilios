<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\UserSessionMaterialStatusDTO;
use App\RelationshipVoter\UserSessionMaterialStatusDTOVoter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @package App\Tests\RelationshipVoter
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\RelationshipVoter\UserSessionMaterialStatusDTOVoter::class)]
class UserSessionMaterialStatusDTOVoterTest extends AbstractBase
{
    protected UserSessionMaterialStatusDTOVoter $dto;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new UserSessionMaterialStatusDTOVoter($this->permissionChecker);
    }

    public function testCanViewDTOifYourself(): void
    {
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = $userId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testRootCanNotViewDTO(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn(13);
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = 1;
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanNotViewDTO(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = $dtoId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [UserSessionMaterialStatusDTO::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, false],
            [VoterPermissions::DELETE, false],
            [VoterPermissions::EDIT, false],
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
