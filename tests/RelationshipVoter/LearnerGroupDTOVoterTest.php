<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\DTO\LearnerGroupDTO;
use App\RelationshipVoter\LearnerGroupDTOVoter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class LearnerGroupDTOVoterTest
 * @package App\Tests\RelationshipVoter
 * @coversDefaultClass \App\RelationshipVoter\LearnerGroupDTOVoter
 */
class LearnerGroupDTOVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new LearnerGroupDTOVoter($this->permissionChecker);
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testRootCanViewDTO()
    {
        $token = $this->createMockTokenWithRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testUserCanViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $dto->id = 1;
        $this->permissionChecker->shouldReceive('canViewLearnerGroup')->andReturn(true);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    /**
     * @covers ::voteOnAttribute()
     */
    public function testCanNotViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock(LearnerGroupDTO::class);
        $dto->id = 1;
        $this->permissionChecker->shouldReceive('canViewLearnerGroup')->andReturn(false);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View not allowed");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [LearnerGroupDTO::class, true],
            [self::class, false],
        ];
    }

    public static function supportsAttributesProvider(): array
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
