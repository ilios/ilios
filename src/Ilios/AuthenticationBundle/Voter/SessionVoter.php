<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SessionInterface $session
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $session, TokenInterface $token)
    {
        $course = $session->getCourse();
        if (! $course) {
            return false;
        }
        // grant perms based on the owning course
        return parent::voteOnAttribute($attribute, $course, $token);
    }

    /**
     * {@inheritdoc}
     */
    protected function isWriteGranted(CourseInterface $course, $user)
    {
        // prevent any sort of write operation (create/edit/delete) if the parent course is locked or archived.
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }
        return parent::isWriteGranted($course, $user);
    }
}
