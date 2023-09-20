<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\UserDTO;
use App\RelationshipVoter\UserDTOVoter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class UserDTOVoterTest
 * @package App\Tests\RelationshipVoter
 */
class UserDTOVoterTest extends AbstractBase
{
    protected $dto;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new UserDTOVoter($this->permissionChecker);
    }

    /**
     * @covers \App\RelationshipVoter\UserDTOVoter::voteOnAttribute
     */
    public function testCanViewDTOAsNonLearner()
    {
        $dtoId = 1;
        $userId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $dto = m::mock(UserDTO::class);
        $dto->id = $dtoId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers \App\RelationshipVoter\UserDTOVoter::voteOnAttribute
     */
    public function testCanViewDTOifYourself()
    {
        $userId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldNotReceive('performsNonLearnerFunction');
        $dto = m::mock(UserDTO::class);
        $dto->id = $userId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers \App\RelationshipVoter\UserDTOVoter::voteOnAttribute
     */
    public function testRootCanViewDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock(UserDTO::class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers \App\RelationshipVoter\UserDTOVoter::voteOnAttribute
     */
    public function testCanNotViewDTO()
    {
        $dtoId = 1;
        $userId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $user->shouldReceive('getId')->andReturn($userId);
        $user->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $dto = m::mock(UserDTO::class);
        $dto->id = $dtoId;

        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [UserDTO::class, true],
            [self::class, false],
        ];
    }

    public function supportsAttributesProvider(): array
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
