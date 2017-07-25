<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionEntityVoter
 */
class SessionEntityVoter extends CourseEntityVoter
{
    /**
     * @inheritdoc
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
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        $course = $session->getCourse();

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($course->getId(), $course->getSchool()->getId(), $user);
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
