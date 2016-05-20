<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserMadeReminderManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findUserMadeReminderBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findUserMadeRemindersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($userMadeReminder, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder
    ) {
        $this->delete($userMadeReminder);
    }

    /**
     * @deprecated
     */
    public function createUserMadeReminder()
    {
        return $this->create();
    }
}
