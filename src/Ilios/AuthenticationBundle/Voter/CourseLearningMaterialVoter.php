<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CourseLearningMaterialVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CourseLearningMaterialInterface');
    }

    /**
     * @param string $attribute
     * @param CourseLearningMaterialInterface $material
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $material, $user = null)
    {
        $course = $material->getCourse();
        if (! $course) {
            return false;
        }
        // grant perms based on the owning session
        return parent::isGranted($attribute, $course, $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isWriteGranted($course, $user)
    {
        // prevent any sort of write operation (create/edit/delete) if the parent course is locked or archived.
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }
        return parent::isWriteGranted($course, $user);
    }
}
