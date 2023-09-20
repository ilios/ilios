<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\RelationshipVoter\Authentication as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthenticationTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(AuthenticationInterface::class));
    }


    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
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

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
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

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
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

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
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

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AuthenticationInterface::class);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getUser')->andReturn($user);
        $this->permissionChecker->shouldReceive('canUpdateUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [AuthenticationInterface::class, true],
            [self::class, false],
        ];
    }

    public function supportsAttributesProvider(): array
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
