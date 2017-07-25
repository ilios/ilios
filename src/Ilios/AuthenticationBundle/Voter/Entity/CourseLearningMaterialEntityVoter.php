<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseLearningMaterialEntityVoter
 */
class CourseLearningMaterialEntityVoter extends CourseEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseLearningMaterialInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CourseLearningMaterialInterface $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        $course = $material->getCourse();
        if (! $course) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                $granted =  $this->isViewGranted($course->getId(), $course->getSchool()->getId(), $user);
                // prevent access if associated LM is in draft, and the current user has no elevated privileges.
                if ($granted) {
                    $granted = $user->hasRole(['Faculty', 'Course Director', 'Developer'])
                    || LearningMaterialStatusInterface::IN_DRAFT !== $material->getLearningMaterial()
                            ->getStatus()->getId();
                }

                return $granted;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // prevent any sort of write operation (create/edit/delete) if the parent course is locked or archived.
                if ($course->isLocked() || $course->isArchived()) {
                    return false;
                }
                return $this->isWriteGranted($course->getId(), $course->getSchool()->getId(), $user);
                break;
        }
        return false;
    }
}
