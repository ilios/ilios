<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\SessionLearningMaterialDTO;
use App\RelationshipVoter\SessionLearningMaterialDTOVoter;
use App\Service\SessionUserPermissionChecker;
use DateTime;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class SessionLearningMaterialDTOVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new SessionLearningMaterialDTOVoter($this->permissionChecker);
    }

    public function testCanViewDTOAsNonLearner(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testRootCanViewDTO(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testCanNotViewDTO(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $user->shouldReceive('isLearnerInSession')->with(13)->andReturn(false);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;
        $dto->session = 13;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanViewDTOIfInSession(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $user->shouldReceive('isLearnerInSession')->with(13)->andReturn(true);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;
        $dto->session = 13;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testCanNotViewDTOBeforeItIsAvailable(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;
        $dto->startDate = new DateTime('tomorrow');

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanNotViewDTOAfterItIsAvailable(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;
        $dto->endDate = new DateTime('yesterday');

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanViewDTOBetweenStartAndEndDates(): void
    {
        $dtoId = 1;
        $userId = 2;
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $user->shouldReceive('isLearnerInSession')->with(13)->andReturn(true);
        $dto = m::mock(SessionLearningMaterialDTO::class);
        $dto->id = $dtoId;
        $dto->session = 13;
        $dto->startDate = new DateTime('yesterday');
        $dto->endDate = new DateTime('tomorrow');

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [SessionLearningMaterialDTO::class, true],
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
