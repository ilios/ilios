<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\AamcPcrsDTO;
use App\Entity\DTO\AamcResourceTypeDTO;
use App\Entity\DTO\AssessmentOptionDTO;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\CohortDTO;
use App\Entity\DTO\CompetencyDTO;
use App\Entity\DTO\CourseClerkshipTypeDTO;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\CourseObjectiveDTO;
use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use App\Entity\DTO\CurriculumInventoryInstitutionDTO;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use App\Entity\DTO\CurriculumInventorySequenceDTO;
use App\Entity\DTO\IlmSessionDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\InstructorGroupDTO;
use App\Entity\DTO\LearnerGroupDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\LearningMaterialStatusDTO;
use App\Entity\DTO\LearningMaterialUserRoleDTO;
use App\Entity\DTO\MeshConceptDTO;
use App\Entity\DTO\MeshDescriptorDTO;
use App\Entity\DTO\MeshPreviousIndexingDTO;
use App\Entity\DTO\MeshQualifierDTO;
use App\Entity\DTO\MeshTermDTO;
use App\Entity\DTO\MeshTreeDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use App\Entity\DTO\ProgramDTO;
use App\Entity\DTO\ProgramYearDTO;
use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Entity\DTO\SchoolConfigDTO;
use App\Entity\DTO\SchoolDTO;
use App\Entity\DTO\SessionDTO;
use App\Entity\DTO\SessionLearningMaterialDTO;
use App\Entity\DTO\SessionObjectiveDTO;
use App\Entity\DTO\SessionTypeDTO;
use App\Entity\DTO\TermDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\DTO\UserRoleDTO;
use App\Entity\DTO\VocabularyDTO;
use App\ServiceTokenVoter\GreenlightViewDTOVoter;

final class GreenlightViewDTOVoterTest extends AbstractReadonlyBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new GreenlightViewDTOVoter();
    }

    public static function subjectProvider(): array
    {
        return [
            [AamcMethodDTO::class],
            [AamcPcrsDTO::class],
            [AamcResourceTypeDTO::class],
            [AssessmentOptionDTO::class],
            [AuthenticationDTO::class],
            [CohortDTO::class],
            [CompetencyDTO::class],
            [CourseClerkshipTypeDTO::class],
            [CourseDTO::class],
            [CourseLearningMaterialDTO::class],
            [CourseObjectiveDTO::class],
            [CurriculumInventoryAcademicLevelDTO::class],
            [CurriculumInventoryInstitutionDTO::class],
            [CurriculumInventoryReportDTO::class],
            [CurriculumInventorySequenceBlockDTO::class],
            [CurriculumInventorySequenceDTO::class],
            [IlmSessionDTO::class],
            [IngestionExceptionDTO::class],
            [InstructorGroupDTO::class],
            [LearnerGroupDTO::class],
            [LearningMaterialDTO::class],
            [LearningMaterialStatusDTO::class],
            [LearningMaterialUserRoleDTO::class],
            [MeshConceptDTO::class],
            [MeshDescriptorDTO::class],
            [MeshPreviousIndexingDTO::class],
            [MeshQualifierDTO::class],
            [MeshTermDTO::class],
            [MeshTreeDTO::class],
            [OfferingDTO::class],
            [PendingUserUpdateDTO::class],
            [ProgramDTO::class],
            [ProgramYearDTO::class],
            [ProgramYearObjectiveDTO::class],
            [SchoolConfigDTO::class],
            [SchoolDTO::class],
            [SessionDTO::class],
            [SessionLearningMaterialDTO::class],
            [SessionObjectiveDTO::class],
            [SessionTypeDTO::class],
            [TermDTO::class],
            [UserDTO::class],
            [UserRoleDTO::class],
            [VocabularyDTO::class],
        ];
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [AamcMethodDTO::class, true],
            [AamcPcrsDTO::class, true],
            [AamcResourceTypeDTO::class, true],
            [AssessmentOptionDTO::class, true],
            [AuthenticationDTO::class, true],
            [CohortDTO::class, true],
            [CompetencyDTO::class, true],
            [CourseClerkshipTypeDTO::class, true],
            [CourseDTO::class, true],
            [CourseLearningMaterialDTO::class, true],
            [CourseObjectiveDTO::class, true],
            [CurriculumInventoryAcademicLevelDTO::class, true],
            [CurriculumInventoryInstitutionDTO::class, true],
            [CurriculumInventoryReportDTO::class, true],
            [CurriculumInventorySequenceBlockDTO::class, true],
            [CurriculumInventorySequenceDTO::class, true],
            [IlmSessionDTO::class, true],
            [IngestionExceptionDTO::class, true],
            [InstructorGroupDTO::class, true],
            [LearnerGroupDTO::class, true],
            [LearningMaterialDTO::class, true],
            [LearningMaterialStatusDTO::class, true],
            [LearningMaterialUserRoleDTO::class, true],
            [MeshConceptDTO::class, true],
            [MeshDescriptorDTO::class, true],
            [MeshPreviousIndexingDTO::class, true],
            [MeshQualifierDTO::class, true],
            [MeshTermDTO::class, true],
            [MeshTreeDTO::class, true],
            [OfferingDTO::class, true],
            [PendingUserUpdateDTO::class, true],
            [ProgramDTO::class, true],
            [ProgramYearDTO::class, true],
            [ProgramYearObjectiveDTO::class, true],
            [SchoolConfigDTO::class, true],
            [SchoolDTO::class, true],
            [SessionDTO::class, true],
            [SessionLearningMaterialDTO::class, true],
            [SessionObjectiveDTO::class, true],
            [SessionTypeDTO::class, true],
            [TermDTO::class, true],
            [UserDTO::class, true],
            [UserRoleDTO::class, true],
            [VocabularyDTO::class, true],
            [self::class, false],
        ];
    }
}
