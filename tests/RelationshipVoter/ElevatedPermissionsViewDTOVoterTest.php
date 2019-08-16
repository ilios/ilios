<?php

namespace App\Tests\RelationshipVoter;

use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\SessionLearningMaterialDTO;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\ElevatedPermissionsViewDTOVoter as Voter;
use App\Service\PermissionChecker;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ElevatedPermissionsViewDtoVoterTest
 * @package App\Tests\RelationshipVoter
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
            [CourseLearningMaterialDTO::class],
            [IngestionExceptionDTO::class],
            [LearningMaterialDTO::class],
            [OfferingDTO::class],
            [PendingUserUpdateDTO::class],
            [SessionLearningMaterialDTO::class],
        ];
    }

    /**
     * @dataProvider dtoProvider
     * @covers ElevatedPermissionsViewDTOVoter::voteOnAttribute()
     */
    public function testCanViewDTO($class)
    {
        $token = $this->createMockTokenWithSessionUserPerformingNonLearnerFunction();
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
        $token = $this->createMockTokenWithRootSessionUser();
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
        $token = $this->createMockTokenWithSessionUserPerformingOnlyLearnerFunction();
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }
}
