<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Entity\DTO\CourseObjectiveDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Entity\DTO\SessionObjectiveDTO;
use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\GreenlightViewDTOVoter as Voter;
use App\Service\PermissionChecker;
use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\AamcPcrsDTO;
use App\Entity\DTO\AamcResourceTypeDTO;
use App\Entity\DTO\AssessmentOptionDTO;
use App\Entity\DTO\CohortDTO;
use App\Entity\DTO\CompetencyDTO;
use App\Entity\DTO\CourseClerkshipTypeDTO;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use App\Entity\DTO\CurriculumInventoryInstitutionDTO;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use App\Entity\DTO\CurriculumInventorySequenceDTO;
use App\Entity\DTO\IlmSessionDTO;
use App\Entity\DTO\InstructorGroupDTO;
use App\Entity\DTO\LearningMaterialStatusDTO;
use App\Entity\DTO\LearningMaterialUserRoleDTO;
use App\Entity\DTO\MeshConceptDTO;
use App\Entity\DTO\MeshDescriptorDTO;
use App\Entity\DTO\MeshPreviousIndexingDTO;
use App\Entity\DTO\MeshQualifierDTO;
use App\Entity\DTO\MeshTermDTO;
use App\Entity\DTO\MeshTreeDTO;
use App\Entity\DTO\ProgramDTO;
use App\Entity\DTO\ProgramYearDTO;
use App\Entity\DTO\SchoolConfigDTO;
use App\Entity\DTO\SchoolDTO;
use App\Entity\DTO\SessionDTO;
use App\Entity\DTO\SessionTypeDTO;
use App\Entity\DTO\TermDTO;
use App\Entity\DTO\UserRoleDTO;
use App\Entity\DTO\VocabularyDTO;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class GreenlightViewDtoVoterTest
 * @package App\Tests\RelationshipVoter
 * @coversDefaultClass \App\RelationshipVoter\GreenlightViewDTOVoter
 */
class GreenlightViewDtoVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public static function canViewDTOProvider()
    {
        return [
            [AamcMethodDTO::class],
            [AamcPcrsDTO::class],
            [AamcResourceTypeDTO::class],
            [AssessmentOptionDTO::class],
            [CohortDTO::class],
            [CompetencyDTO::class],
            [CourseDTO::class],
            [CourseClerkshipTypeDTO::class],
            [CourseObjectiveDTO::class],
            [CurriculumInventoryAcademicLevelDTO::class],
            [CurriculumInventoryInstitutionDTO::class],
            [CurriculumInventoryReportDTO::class],
            [CurriculumInventorySequenceDTO::class],
            [CurriculumInventorySequenceBlockDTO::class],
            [IlmSessionDTO::class],
            [InstructorGroupDTO::class],
            [LearningMaterialDTO::class],
            [LearningMaterialStatusDTO::class],
            [LearningMaterialUserRoleDTO::class],
            [MeshConceptDTO::class],
            [MeshDescriptorDTO::class],
            [MeshPreviousIndexingDTO::class],
            [MeshQualifierDTO::class],
            [MeshTermDTO::class],
            [MeshTreeDTO::class],
            [ProgramDTO::class],
            [ProgramYearDTO::class],
            [ProgramYearObjectiveDTO::class],
            [SchoolDTO::class],
            [SchoolConfigDTO::class],
            [SessionDTO::class],
            [SessionObjectiveDTO::class],
            [SessionTypeDTO::class],
            [TermDTO::class],
            [UserRoleDTO::class],
            [VocabularyDTO::class],
        ];
    }

    /**
     * @covers ::voteOnAttribute()
     * @param string $class The fully qualified class name.
     */
    #[DataProvider('canViewDTOProvider')]
    public function testCanViewDTO($class)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }
}
