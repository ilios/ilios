<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\AamcMethodDTO;
use Ilios\CoreBundle\Entity\DTO\AamcPcrsDTO;
use Ilios\CoreBundle\Entity\DTO\AamcResourceTypeDTO;
use Ilios\CoreBundle\Entity\DTO\AssessmentOptionDTO;
use Ilios\CoreBundle\Entity\DTO\CohortDTO;
use Ilios\CoreBundle\Entity\DTO\CompetencyDTO;
use Ilios\CoreBundle\Entity\DTO\CourseClerkshipTypeDTO;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryReportDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceDTO;
use Ilios\CoreBundle\Entity\DTO\DepartmentDTO;
use Ilios\CoreBundle\Entity\DTO\IlmSessionDTO;
use Ilios\CoreBundle\Entity\DTO\InstructorGroupDTO;
use Ilios\CoreBundle\Entity\DTO\LearningMaterialStatusDTO;
use Ilios\CoreBundle\Entity\DTO\MeshConceptDTO;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\DTO\MeshPreviousIndexingDTO;
use Ilios\CoreBundle\Entity\DTO\MeshQualifierDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTreeDTO;
use Ilios\CoreBundle\Entity\DTO\ObjectiveDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramYearDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramYearStewardDTO;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\DTO\SessionDescriptionDTO;
use Ilios\CoreBundle\Entity\DTO\SessionDTO;
use Ilios\CoreBundle\Entity\DTO\SessionTypeDTO;
use Ilios\CoreBundle\Entity\DTO\TermDTO;
use Ilios\CoreBundle\Entity\DTO\VocabularyDTO;
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
            array($attribute, [self::VIEW]) && (
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
                || $subject instanceof SessionDTO
                || $subject instanceof SessionDescriptionDTO
                || $subject instanceof SessionTypeDTO
                || $subject instanceof TermDTO
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
