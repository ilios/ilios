<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use App\RelationshipVoter\ElevatedPermissionsViewDTOVoter as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ElevatedPermissionsViewDtoVoterTest
 * @package App\Tests\RelationshipVoter
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\RelationshipVoter\ElevatedPermissionsViewDTOVoter::class)]
class ElevatedPermissionsViewDTOVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter();
    }

    public static function dtoProvider(): array
    {
        return [
            [AuthenticationDTO::class],
            [CourseLearningMaterialDTO::class],
            [IngestionExceptionDTO::class],
            [OfferingDTO::class],
            [PendingUserUpdateDTO::class],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dtoProvider')]
    public function testCanViewDTO(string $class): void
    {
        $user = $this->createMockSessionUserPerformingNonLearnerFunction();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dtoProvider')]
    public function testRootCanViewDTO(string $class): void
    {
        $user = $this->createMockRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dtoProvider')]
    public function testCanNotViewDTO(string $class): void
    {
        $user = $this->createMockSessionUserPerformingOnlyLearnerFunction();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [AuthenticationDTO::class, true],
            [CourseLearningMaterialDTO::class, true],
            [IngestionExceptionDTO::class, true],
            [OfferingDTO::class, true],
            [PendingUserUpdateDTO::class, true],
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
