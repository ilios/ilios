<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\AamcMethodInterface;
use App\Entity\AamcPcrsInterface;
use App\Entity\AamcResourceTypeInterface;
use App\Entity\AssessmentOptionInterface;
use App\Entity\CourseClerkshipTypeInterface;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\IngestionExceptionInterface;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;
use App\Entity\LearningMaterialUserRoleInterface;
use App\Entity\MeshConceptInterface;
use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshPreviousIndexingInterface;
use App\Entity\MeshQualifierInterface;
use App\Entity\MeshTermInterface;
use App\Entity\MeshTreeInterface;
use App\Entity\UserInterface;
use App\Entity\UserRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReadonlyEntityVoter extends Voter
{
    public function supportsAttribute(string $attribute): bool
    {
        return $attribute === VoterPermissions::VIEW;
    }

    public function supportsType(string $subjectType): bool
    {
        return (
            is_a($subjectType, AamcMethodInterface::class, true)
            || is_a($subjectType, AamcPcrsInterface::class, true)
            || is_a($subjectType, AamcResourceTypeInterface::class, true)
            || is_a($subjectType, AssessmentOptionInterface::class, true)
            || is_a($subjectType, CourseClerkshipTypeInterface::class, true)
            || is_a($subjectType, CurriculumInventoryAcademicLevelInterface::class, true)
            || is_a($subjectType, IngestionExceptionInterface::class, true)
            || is_a($subjectType, LearningMaterialInterface::class, true)
            || is_a($subjectType, LearningMaterialStatusInterface::class, true)
            || is_a($subjectType, LearningMaterialUserRoleInterface::class, true)
            || is_a($subjectType, MeshConceptInterface::class, true)
            || is_a($subjectType, MeshDescriptorInterface::class, true)
            || is_a($subjectType, MeshPreviousIndexingInterface::class, true)
            || is_a($subjectType, MeshQualifierInterface::class, true)
            || is_a($subjectType, MeshTermInterface::class, true)
            || is_a($subjectType, MeshTreeInterface::class, true)
            || is_a($subjectType, UserInterface::class, true)
            || is_a($subjectType, UserRoleInterface::class, true)
        );
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $this->supportsAttribute($attribute) && (
                $subject instanceof AamcMethodInterface ||
                $subject instanceof AamcPcrsInterface ||
                $subject instanceof AamcResourceTypeInterface ||
                $subject instanceof AssessmentOptionInterface ||
                $subject instanceof CourseClerkshipTypeInterface ||
                $subject instanceof CurriculumInventoryAcademicLevelInterface ||
                $subject instanceof IngestionExceptionInterface ||
                $subject instanceof LearningMaterialInterface ||
                $subject instanceof LearningMaterialStatusInterface ||
                $subject instanceof LearningMaterialUserRoleInterface ||
                $subject instanceof MeshConceptInterface ||
                $subject instanceof MeshDescriptorInterface ||
                $subject instanceof MeshPreviousIndexingInterface ||
                $subject instanceof MeshQualifierInterface ||
                $subject instanceof MeshTermInterface ||
                $subject instanceof MeshTreeInterface ||
                $subject instanceof UserInterface ||
                $subject instanceof UserRoleInterface
            )
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof ServiceTokenUserInterface) {
            return false;
        }

        return true;
    }
}
