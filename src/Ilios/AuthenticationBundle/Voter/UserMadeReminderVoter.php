<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserMadeReminderInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserMadeReminderVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserMadeReminderVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserMadeReminderInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param UserMadeReminderInterface $reminder
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $reminder, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // Users can perform any CRUD operations on their own reminders.
            // Check if the given reminder's owning user is the given user.
            case self::CREATE:
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->usersAreIdentical($user, $reminder->getUser());
                break;
        }

        return false;
    }
}
