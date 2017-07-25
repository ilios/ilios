<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Ilios\CoreBundle\Entity\DTO\SessionDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionDTOVoter
 */
class SessionDTOVoter extends CourseVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param SessionDTO $session
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $session, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        $course = $this->courseManager->findOneBy(['id' => $session->course]);

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($course->getId(), $course->getSchool()->getId(), $user);
                break;
        }
        return false;
    }
}
