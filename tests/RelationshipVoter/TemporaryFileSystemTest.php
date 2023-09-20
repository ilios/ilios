<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\RelationshipVoter\TemporaryFileSystem as Voter;
use App\Service\SessionUserPermissionChecker;
use App\Service\TemporaryFileSystem;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TemporaryFileSystemTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(TemporaryFileSystem::class), [VoterPermissions::CREATE]);
    }

    public function testCanCreateTemporaryFileSystem()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TemporaryFileSystem::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreateTemporaryFileSystem()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TemporaryFileSystem::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [TemporaryFileSystem::class, true],
            [self::class, false],
        ];
    }

    public function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, false],
            [VoterPermissions::CREATE, true],
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
