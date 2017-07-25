<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CourseLearningMaterialDTO;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseLearningMaterialDTOVoter
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
        }
        return false;
    }
}
