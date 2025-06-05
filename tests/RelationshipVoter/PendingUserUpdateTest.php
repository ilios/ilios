<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\PendingUserUpdateInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\RelationshipVoter\PendingUserUpdate as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class PendingUserUpdateTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess(): void
    {
        $this->checkRootEntityAccess(
            m::mock(PendingUserUpdateInterface::class),
            [VoterPermissions::VIEW, VoterPermissions::DELETE, VoterPermissions::EDIT]
        );
    }


    public function testCanView(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotView(): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanEdit(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete(): void
    {
        $sessionUser = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $entity = m::mock(PendingUserUpdateInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [PendingUserUpdateInterface::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, false],
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
