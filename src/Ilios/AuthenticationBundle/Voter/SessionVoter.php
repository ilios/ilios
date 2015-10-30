<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\SessionInterface');
    }

    /**
     * @param string $attribute
     * @param SessionInterface $session
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $session, $user = null)
    {
        $course = $session->getCourse();
        if (! $course) {
            return false;
        }
        // grant perms based on the owning course
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
