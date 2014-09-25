<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\UserMadeReminderInterface;

/**
 * Interface UserMadeReminderManagerInterface
 */
interface UserMadeReminderManagerInterface
{
    /** 
     *@return UserMadeReminderInterface
     */
    public function createUserMadeReminder();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserMadeReminderInterface
     */
    public function findUserMadeReminderBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserMadeReminderInterface[]|Collection
     */
    public function findUserMadeRemindersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUserMadeReminder(UserMadeReminderInterface $userMadeReminder, $andFlush = true);

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     *
     * @return void
     */
    public function deleteUserMadeReminder(UserMadeReminderInterface $userMadeReminder);

    /**
     * @return string
     */
    public function getClass();
}
