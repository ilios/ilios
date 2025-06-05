<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\VoterPermissions;
use App\Entity\MeshConceptInterface;
use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshPreviousIndexingInterface;
use App\Entity\MeshQualifierInterface;
use App\Entity\MeshTermInterface;
use App\Entity\MeshTreeInterface;
use App\RelationshipVoter\Mesh as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class MeshTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter();
    }

    public static function meshEntitiesProvider(): array
    {
        return [
            [MeshConceptInterface::class],
            [MeshDescriptorInterface::class],
            [MeshPreviousIndexingInterface::class],
            [MeshQualifierInterface::class],
            [MeshTermInterface::class],
            [MeshTreeInterface::class],
        ];
    }

    #[DataProvider('meshEntitiesProvider')]
    public function testAllowsRootFullAccess(string $className): void
    {
        $this->checkRootEntityAccess(m::mock($className), [VoterPermissions::VIEW]);
    }

    #[DataProvider('meshEntitiesProvider')]
    public function testCanView(string $className): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock($className);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [MeshConceptInterface::class, true],
            [MeshDescriptorInterface::class, true],
            [MeshPreviousIndexingInterface::class, true],
            [MeshQualifierInterface::class, true],
            [MeshTermInterface::class, true],
            [MeshTreeInterface::class, true],
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
