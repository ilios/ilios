<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\UserSessionMaterialStatusDTO;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\UserDTOVoter;
use App\RelationshipVoter\UserSessionMaterialStatusDTOVoter;
use App\Service\PermissionChecker;
use App\Entity\DTO\UserDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @package App\Tests\RelationshipVoter
 */
class UserSessionMaterialStatusDTOVoterTest extends AbstractBase
{
    protected $dto;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new UserSessionMaterialStatusDTOVoter($this->permissionChecker);
    }

    /**
     * @covers \App\RelationshipVoter\UserSessionMaterialStatusDTOVoter::voteOnAttribute
     */
    public function testCanViewDTOifYourself()
    {
        $userId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $user->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = $userId;

        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers \App\RelationshipVoter\UserSessionMaterialStatusDTOVoter::voteOnAttribute
     */
    public function testRootCanNotViewDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn(13);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = 1;
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    /**
     * @covers \App\RelationshipVoter\UserSessionMaterialStatusDTOVoter::voteOnAttribute
     */
    public function testCanNotViewDTO()
    {
        $dtoId = 1;
        $userId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $user->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(UserSessionMaterialStatusDTO::class);
        $dto->user = $dtoId;

        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }
}
