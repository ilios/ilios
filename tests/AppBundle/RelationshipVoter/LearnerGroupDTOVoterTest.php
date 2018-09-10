<?php

namespace Tests\AppBundle\RelationshipVoter;

use AppBundle\RelationshipVoter\AbstractVoter;
use AppBundle\RelationshipVoter\LearnerGroupDTOVoter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\DTO\LearnerGroupDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class LearnerGroupDTOVoterTest
 * @package Tests\AppBundle\RelationshipVoter
 */
class LearnerGroupDTOVoterTest extends AbstractBase
{
    /**
     * @inheritdoc
     */
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new LearnerGroupDTOVoter($this->permissionChecker);
    }

    /**
     * @covers LearnerGroupDTOVoter::voteOnAttribute()
     */
    public function testRootCanViewDTO()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers LearnerGroupDTOVoter::voteOnAttribute()
     */
    public function testUserCanViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $dto->id = 1;
        $this->permissionChecker->shouldReceive('canViewLearnerGroup')->andReturn(true);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers LearnerGroupDTOVoter::voteOnAttribute()
     */
    public function testCanNotViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $dto->id = 1;
        $this->permissionChecker->shouldReceive('canViewLearnerGroup')->andReturn(false);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View not allowed");
    }
}
