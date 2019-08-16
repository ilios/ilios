<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
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
use App\Entity\DTO\DepartmentDTO;
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
use App\Entity\DTO\ObjectiveDTO;
use App\Entity\DTO\ProgramDTO;
use App\Entity\DTO\ProgramYearDTO;
use App\Entity\DTO\ProgramYearStewardDTO;
use App\Entity\DTO\SchoolConfigDTO;
use App\Entity\DTO\SchoolDTO;
use App\Entity\DTO\SessionDescriptionDTO;
use App\Entity\DTO\SessionDTO;
use App\Entity\DTO\SessionTypeDTO;
use App\Entity\DTO\TermDTO;
use App\Entity\DTO\UserRoleDTO;
use App\Entity\DTO\VocabularyDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Always grants VIEW permissions on all supported DTOs.
 *
 * @package App\RelationshipVoter
 */
class GreenlightViewDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            in_array($attribute, [self::VIEW]) && (
                $subject instanceof AamcMethodDTO
                || $subject instanceof AamcPcrsDTO
                || $subject instanceof AamcResourceTypeDTO
                || $subject instanceof AssessmentOptionDTO
                || $subject instanceof CohortDTO
                || $subject instanceof CompetencyDTO
                || $subject instanceof CourseDTO
                || $subject instanceof CourseClerkshipTypeDTO
                || $subject instanceof CurriculumInventoryAcademicLevelDTO
                || $subject instanceof CurriculumInventoryInstitutionDTO
                || $subject instanceof CurriculumInventoryReportDTO
                || $subject instanceof CurriculumInventorySequenceDTO
                || $subject instanceof CurriculumInventorySequenceBlockDTO
                || $subject instanceof DepartmentDTO
                || $subject instanceof IlmSessionDTO
                || $subject instanceof InstructorGroupDTO
                || $subject instanceof LearningMaterialStatusDTO
                || $subject instanceof LearningMaterialUserRoleDTO
                || $subject instanceof MeshConceptDTO
                || $subject instanceof MeshDescriptorDTO
                || $subject instanceof MeshPreviousIndexingDTO
                || $subject instanceof MeshQualifierDTO
                || $subject instanceof MeshTermDTO
                || $subject instanceof MeshTreeDTO
                || $subject instanceof ObjectiveDTO
                || $subject instanceof ProgramDTO
                || $subject instanceof ProgramYearDTO
                || $subject instanceof ProgramYearStewardDTO
                || $subject instanceof SchoolDTO
                || $subject instanceof SchoolConfigDTO
                || $subject instanceof SessionDTO
                || $subject instanceof SessionDescriptionDTO
                || $subject instanceof SessionTypeDTO
                || $subject instanceof TermDTO
                || $subject instanceof UserRoleDTO
                || $subject instanceof VocabularyDTO
            )
        );
    }
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return true;
    }
}
