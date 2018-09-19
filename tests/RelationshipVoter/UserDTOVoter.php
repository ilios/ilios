<?php

namespace Tests\App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\UserDTOVoter;
use App\Service\PermissionChecker;
use App\Entity\DTO\UserDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class UserDTOVoterTest
 * @package Tests\AppBundle\RelationshipVoter
 */
class UserDTOVoterTest extends AbstractBase
{
    protected $dto;

    /**
     * @inheritdoc
     */
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new UserDTOVoter($this->permissionChecker);
    }

    /**
     * @covers UserDTOVoter::voteOnAttribute()
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

        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers UserDTOVoter::voteOnAttribute()
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

        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers UserDTOVoter::voteOnAttribute()
     */
    public function testRootCanViewDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock(UserDTO::class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers UserDTOVoter::voteOnAttribute()
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

        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }
}
