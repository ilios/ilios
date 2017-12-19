<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\ElevatedPermissionsViewDTOVoter as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;
use Ilios\CoreBundle\Entity\DTO\OfferingDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class GreenlightViewDtoVoterTest
 * @package Tests\AuthenticationBundle\RelationshipVoter
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
            [LearnerGroupDTO::class],
            [OfferingDTO::class],
        ];
    }

    /**
     * @dataProvider dtoProvider
     * @covers GreenlightViewDTOVoter::voteOnAttribute()
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
     * @covers GreenlightViewDTOVoter::voteOnAttribute()
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
     * @covers GreenlightViewDTOVoter::voteOnAttribute()
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
