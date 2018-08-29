<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\UserDTOVoter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use AppBundle\Entity\DTO\UserDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class UserDTOVoterTest
 * @package Tests\AuthenticationBundle\RelationshipVoter
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
