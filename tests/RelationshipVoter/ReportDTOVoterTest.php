<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\ReportDTOVoter;
use App\Service\PermissionChecker;
use App\Entity\DTO\ReportDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ReportDTOVoterTest
 * @package App\Tests\RelationshipVoter
 * @coversDefaultClass \App\RelationshipVoter\ReportDTOVoter

 */
class ReportDTOVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new ReportDTOVoter($this->permissionChecker);
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testCanViewDTO()
    {
        $userId = 1;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $token->getUser()->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(ReportDTO::class);
        $dto->user = $userId;
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testRootCanViewDTO()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $dto = m::mock(ReportDTO::class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testCanNotViewDTO()
    {
        $userId = 1;
        $reportOwnerId = 2;
        $token = $this->createMockTokenWithNonRootSessionUser();
        $token->getUser()->shouldReceive('getId')->andReturn($userId);
        $dto = m::mock(ReportDTO::class);
        $dto->user = $reportOwnerId;
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View allowed");
    }
}
