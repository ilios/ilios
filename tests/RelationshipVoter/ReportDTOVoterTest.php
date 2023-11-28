<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\DTO\ReportDTO;
use App\RelationshipVoter\ReportDTOVoter;
use App\Service\SessionUserPermissionChecker;
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
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
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
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testRootCanViewDTO()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $dto = m::mock(ReportDTO::class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
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
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View allowed");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [ReportDTO::class, true],
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
