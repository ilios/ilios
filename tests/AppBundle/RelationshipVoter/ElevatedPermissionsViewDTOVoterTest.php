<?php

namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\ElevatedPermissionsViewDTOVoter as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\DTO\AuthenticationDTO;
use AppBundle\Entity\DTO\IngestionExceptionDTO;
use AppBundle\Entity\DTO\LearnerGroupDTO;
use AppBundle\Entity\DTO\OfferingDTO;
use AppBundle\Entity\DTO\PendingUserUpdateDTO;
use AppBundle\Entity\DTO\UserDTO;
use AppBundle\Service\Config;
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
