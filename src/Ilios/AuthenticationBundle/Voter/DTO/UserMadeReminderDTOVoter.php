<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\UserMadeReminderDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserMadeReminderDTOVoter
 */
class UserMadeReminderDTOVoter extends AbstractVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserMadeReminderDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param UserMadeReminderDTO $userMadeReminder
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $userMadeReminder, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $user->getId() === $userMadeReminder->user;
                break;
        }
        return false;
    }
}
