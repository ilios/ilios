<?php

namespace Tests\App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\ElevatedPermissionsViewDTOVoter as Voter;
use App\Service\PermissionChecker;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\LearnerGroupDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use App\Entity\DTO\UserDTO;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ElevatedPermissionsViewDtoVoterTest
 * @package Tests\AppBundle\RelationshipVoter
 */
class ElevatedPermissionsViewDTOVoterTest extends AbstractBase
{
    /**
     * @inheritdoc
     */
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function dtoProvider()
    {
        return [
            [AuthenticationDTO::class],
            [IngestionExceptionDTO::class],
            [OfferingDTO::class],
            [PendingUserUpdateDTO::class],
        ];
    }

    /**
     * @dataProvider dtoProvider
     * @covers ElevatedPermissionsViewDTOVoter::voteOnAttribute()
     */
    public function testCanViewDTO($class)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @dataProvider dtoProvider
     * @covers ElevatedPermissionsViewDTOVoter::voteOnAttribute()
     */
    public function testRootCanViewDTO($class)
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @dataProvider dtoProvider
     * @covers ElevatedPermissionsViewDTOVoter::voteOnAttribute()
     */
    public function testCanNotViewDTO($class)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }
}
