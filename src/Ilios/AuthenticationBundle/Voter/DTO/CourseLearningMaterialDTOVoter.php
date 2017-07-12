<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CourseLearningMaterialDTO;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseLearningMaterialDTOVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CourseLearningMaterialDTOVoter extends CourseDTOVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseLearningMaterialDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CourseLearningMaterialDTO $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        $courseId = $material->course;
        $schoolId = $material->school;
        if (! $courseId || ! $schoolId) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                $granted =  $this->isViewGranted($courseId, $schoolId, $user);
                // prevent access if associated LM is in draft, and the current user has no elevated privileges.
                if ($granted) {
                    $granted = $user->hasRole(['Faculty', 'Course Director', 'Developer'])
                    || LearningMaterialStatusInterface::IN_DRAFT !== $material->status;
                }

                return $granted;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // prevent any sort of write operation (create/edit/delete) if the parent course is locked or archived.
                if ($material->courseIsLocked || $material->courseIsArchived) {
                    return false;
                }
                return $this->isWriteGranted($courseId, $schoolId, $user);
                break;
        }
        return false;
    }
}
