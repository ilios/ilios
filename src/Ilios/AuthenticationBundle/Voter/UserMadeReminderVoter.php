<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserMadeReminderInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserMadeReminderVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserMadeReminderVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\UserMadeReminderInterface');
    }

    /**
     * @param string $attribute
     * @param UserMadeReminderInterface $reminder
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $reminder, $user = null)
    {
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
