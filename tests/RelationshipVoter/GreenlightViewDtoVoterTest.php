<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\DTO\CourseObjectiveDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Entity\DTO\SessionObjectiveDTO;
use App\RelationshipVoter\GreenlightViewDTOVoter as Voter;
use App\Service\SessionUserPermissionChecker;
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
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class GreenlightViewDtoVoterTest
 * @package App\Tests\RelationshipVoter
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\RelationshipVoter\GreenlightViewDTOVoter::class)]
class GreenlightViewDtoVoterTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter();
    }

    public static function canViewDTOProvider(): array
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
     * @param string $class The fully qualified class name.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('canViewDTOProvider')]
    public function testCanViewDTO(string $class): void
    {
        $user = $this->createMockNonRootSessionUser();
        $token = $this->createMockTokenWithMockSessionUser($user);
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [AamcMethodDTO::class, true],
            [AamcPcrsDTO::class, true],
            [AamcResourceTypeDTO::class, true],
            [AssessmentOptionDTO::class, true],
            [CohortDTO::class, true],
            [CompetencyDTO::class, true],
            [CourseDTO::class, true],
            [CourseClerkshipTypeDTO::class, true],
            [CourseObjectiveDTO::class, true],
            [CurriculumInventoryAcademicLevelDTO::class, true],
            [CurriculumInventoryInstitutionDTO::class, true],
            [CurriculumInventoryReportDTO::class, true],
            [CurriculumInventorySequenceDTO::class, true],
            [CurriculumInventorySequenceBlockDTO::class, true],
            [IlmSessionDTO::class, true],
            [InstructorGroupDTO::class, true],
            [LearningMaterialDTO::class, true],
            [LearningMaterialStatusDTO::class, true],
            [LearningMaterialUserRoleDTO::class, true],
            [MeshConceptDTO::class, true],
            [MeshDescriptorDTO::class, true],
            [MeshPreviousIndexingDTO::class, true],
            [MeshQualifierDTO::class, true],
            [MeshTermDTO::class, true],
            [MeshTreeDTO::class, true],
            [ProgramDTO::class, true],
            [ProgramYearDTO::class, true],
            [ProgramYearObjectiveDTO::class, true],
            [SchoolDTO::class, true],
            [SchoolConfigDTO::class, true],
            [SessionDTO::class, true],
            [SessionObjectiveDTO::class, true],
            [SessionTypeDTO::class, true],
            [TermDTO::class, true],
            [UserRoleDTO::class, true],
            [VocabularyDTO::class, true],
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
