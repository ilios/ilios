<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Ilios\CoreBundle\Entity\DTO\IlmSessionDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class IlmSessionDTOVoter
 */
class IlmSessionDTOVoter extends CourseVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IlmSessionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param IlmSessionDTO $ilmSession
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $ilmSession, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($ilmSession->course, $ilmSession->school, $user);
                break;
        }
        return false;
    }
}
