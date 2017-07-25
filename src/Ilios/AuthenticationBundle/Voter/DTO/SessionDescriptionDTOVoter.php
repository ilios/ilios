<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Ilios\CoreBundle\Entity\DTO\SessionDescriptionDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionDescriptionDTOVoter
 */
class SessionDescriptionDTOVoter extends CourseVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionDescriptionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param SessionDescriptionDTO $sessionDescription
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $sessionDescription, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($sessionDescription->course, $sessionDescription->school, $user);
                break;
        }
        return false;
    }
}
