<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\AamcPcrsDTO;
use App\Entity\DTO\AamcResourceTypeDTO;
use App\Entity\DTO\AssessmentOptionDTO;
use App\Entity\DTO\CohortDTO;
use App\Entity\DTO\CompetencyDTO;
use App\Entity\DTO\CourseClerkshipTypeDTO;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\CourseObjectiveDTO;
use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use App\Entity\DTO\CurriculumInventoryInstitutionDTO;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use App\Entity\DTO\CurriculumInventorySequenceDTO;
use App\Entity\DTO\IlmSessionDTO;
use App\Entity\DTO\InstructorGroupDTO;
use App\Entity\DTO\LearningMaterialDTO;
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
use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Entity\DTO\SchoolConfigDTO;
use App\Entity\DTO\SchoolDTO;
use App\Entity\DTO\SessionDTO;
use App\Entity\DTO\SessionObjectiveDTO;
use App\Entity\DTO\SessionTypeDTO;
use App\Entity\DTO\TermDTO;
use App\Entity\DTO\UserRoleDTO;
use App\Entity\DTO\VocabularyDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Always grants VIEW permissions on all supported DTOs.
 *
 * @package App\RelationshipVoter
 */
class GreenlightViewDTOVoter extends Voter
{
    public function supportsAttribute(string $attribute): bool
    {
        return $attribute === VoterPermissions::VIEW;
    }

    public function supportsType(string $subjectType): bool
    {
        return (
            is_a($subjectType, AamcMethodDTO::class, true)
            || is_a($subjectType, AamcPcrsDTO::class, true)
            || is_a($subjectType, AamcResourceTypeDTO::class, true)
            || is_a($subjectType, AssessmentOptionDTO::class, true)
            || is_a($subjectType, CohortDTO::class, true)
            || is_a($subjectType, CompetencyDTO::class, true)
            || is_a($subjectType, CourseDTO::class, true)
            || is_a($subjectType, CourseClerkshipTypeDTO::class, true)
            || is_a($subjectType, CourseObjectiveDTO::class, true)
            || is_a($subjectType, CurriculumInventoryAcademicLevelDTO::class, true)
            || is_a($subjectType, CurriculumInventoryInstitutionDTO::class, true)
            || is_a($subjectType, CurriculumInventoryReportDTO::class, true)
            || is_a($subjectType, CurriculumInventorySequenceDTO::class, true)
            || is_a($subjectType, CurriculumInventorySequenceBlockDTO::class, true)
            || is_a($subjectType, IlmSessionDTO::class, true)
            || is_a($subjectType, InstructorGroupDTO::class, true)
            || is_a($subjectType, LearningMaterialDTO::class, true)
            || is_a($subjectType, LearningMaterialStatusDTO::class, true)
            || is_a($subjectType, LearningMaterialUserRoleDTO::class, true)
            || is_a($subjectType, MeshConceptDTO::class, true)
            || is_a($subjectType, MeshDescriptorDTO::class, true)
            || is_a($subjectType, MeshPreviousIndexingDTO::class, true)
            || is_a($subjectType, MeshQualifierDTO::class, true)
            || is_a($subjectType, MeshTermDTO::class, true)
            || is_a($subjectType, MeshTreeDTO::class, true)
            || is_a($subjectType, ProgramDTO::class, true)
            || is_a($subjectType, ProgramYearDTO::class, true)
            || is_a($subjectType, ProgramYearObjectiveDTO::class, true)
            || is_a($subjectType, SchoolDTO::class, true)
            || is_a($subjectType, SchoolConfigDTO::class, true)
            || is_a($subjectType, SessionDTO::class, true)
            || is_a($subjectType, SessionObjectiveDTO::class, true)
            || is_a($subjectType, SessionTypeDTO::class, true)
            || is_a($subjectType, TermDTO::class, true)
            || is_a($subjectType, UserRoleDTO::class, true)
            || is_a($subjectType, VocabularyDTO::class, true)
        );
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $this->supportsAttribute($attribute) && (
                $subject instanceof AamcMethodDTO
                || $subject instanceof AamcPcrsDTO
                || $subject instanceof AamcResourceTypeDTO
                || $subject instanceof AssessmentOptionDTO
                || $subject instanceof CohortDTO
                || $subject instanceof CompetencyDTO
                || $subject instanceof CourseDTO
                || $subject instanceof CourseClerkshipTypeDTO
                || $subject instanceof CourseObjectiveDTO
                || $subject instanceof CurriculumInventoryAcademicLevelDTO
                || $subject instanceof CurriculumInventoryInstitutionDTO
                || $subject instanceof CurriculumInventoryReportDTO
                || $subject instanceof CurriculumInventorySequenceDTO
                || $subject instanceof CurriculumInventorySequenceBlockDTO
                || $subject instanceof IlmSessionDTO
                || $subject instanceof InstructorGroupDTO
                || $subject instanceof LearningMaterialDTO
                || $subject instanceof LearningMaterialStatusDTO
                || $subject instanceof LearningMaterialUserRoleDTO
                || $subject instanceof MeshConceptDTO
                || $subject instanceof MeshDescriptorDTO
                || $subject instanceof MeshPreviousIndexingDTO
                || $subject instanceof MeshQualifierDTO
                || $subject instanceof MeshTermDTO
                || $subject instanceof MeshTreeDTO
                || $subject instanceof ProgramDTO
                || $subject instanceof ProgramYearDTO
                || $subject instanceof ProgramYearObjectiveDTO
                || $subject instanceof SchoolDTO
                || $subject instanceof SchoolConfigDTO
                || $subject instanceof SessionDTO
                || $subject instanceof SessionObjectiveDTO
                || $subject instanceof SessionTypeDTO
                || $subject instanceof TermDTO
                || $subject instanceof UserRoleDTO
                || $subject instanceof VocabularyDTO
            )
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return true;
    }
}
