<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\DTO\AamcMethodDTO;
use AppBundle\Entity\DTO\AamcPcrsDTO;
use AppBundle\Entity\DTO\AamcResourceTypeDTO;
use AppBundle\Entity\DTO\AssessmentOptionDTO;
use AppBundle\Entity\DTO\CohortDTO;
use AppBundle\Entity\DTO\CompetencyDTO;
use AppBundle\Entity\DTO\CourseClerkshipTypeDTO;
use AppBundle\Entity\DTO\CourseDTO;
use AppBundle\Entity\DTO\CourseLearningMaterialDTO;
use AppBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use AppBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;
use AppBundle\Entity\DTO\CurriculumInventoryReportDTO;
use AppBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use AppBundle\Entity\DTO\CurriculumInventorySequenceDTO;
use AppBundle\Entity\DTO\DepartmentDTO;
use AppBundle\Entity\DTO\IlmSessionDTO;
use AppBundle\Entity\DTO\InstructorGroupDTO;
use AppBundle\Entity\DTO\LearningMaterialDTO;
use AppBundle\Entity\DTO\LearningMaterialStatusDTO;
use AppBundle\Entity\DTO\LearningMaterialUserRoleDTO;
use AppBundle\Entity\DTO\MeshConceptDTO;
use AppBundle\Entity\DTO\MeshDescriptorDTO;
use AppBundle\Entity\DTO\MeshPreviousIndexingDTO;
use AppBundle\Entity\DTO\MeshQualifierDTO;
use AppBundle\Entity\DTO\MeshTermDTO;
use AppBundle\Entity\DTO\MeshTreeDTO;
use AppBundle\Entity\DTO\ObjectiveDTO;
use AppBundle\Entity\DTO\ProgramDTO;
use AppBundle\Entity\DTO\ProgramYearDTO;
use AppBundle\Entity\DTO\ProgramYearStewardDTO;
use AppBundle\Entity\DTO\SchoolConfigDTO;
use AppBundle\Entity\DTO\SchoolDTO;
use AppBundle\Entity\DTO\SessionDescriptionDTO;
use AppBundle\Entity\DTO\SessionDTO;
use AppBundle\Entity\DTO\SessionLearningMaterialDTO;
use AppBundle\Entity\DTO\SessionTypeDTO;
use AppBundle\Entity\DTO\TermDTO;
use AppBundle\Entity\DTO\UserRoleDTO;
use AppBundle\Entity\DTO\VocabularyDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Always grants VIEW permissions on all supported DTOs.
 *
 * @package Ilios\AuthenticationBundle\RelationshipVoter
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
                || $subject instanceof CourseLearningMaterialDTO
                || $subject instanceof CurriculumInventoryAcademicLevelDTO
                || $subject instanceof CurriculumInventoryInstitutionDTO
                || $subject instanceof CurriculumInventoryReportDTO
                || $subject instanceof CurriculumInventorySequenceDTO
                || $subject instanceof CurriculumInventorySequenceBlockDTO
                || $subject instanceof DepartmentDTO
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
                || $subject instanceof ObjectiveDTO
                || $subject instanceof ProgramDTO
                || $subject instanceof ProgramYearDTO
                || $subject instanceof ProgramYearStewardDTO
                || $subject instanceof SchoolDTO
                || $subject instanceof SchoolConfigDTO
                || $subject instanceof SessionDTO
                || $subject instanceof SessionDescriptionDTO
                || $subject instanceof SessionLearningMaterialDTO
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
