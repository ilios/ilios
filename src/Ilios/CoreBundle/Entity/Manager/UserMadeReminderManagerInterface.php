<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Interface UserMadeReminderManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface UserMadeReminderManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserMadeReminderInterface
     */
    public function findUserMadeReminderBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|UserMadeReminderInterface[]
     */
    public function findUserMadeRemindersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     *
     * @return void
     */
    public function deleteUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder
    );

    /**
     * @return UserMadeReminderInterface
     */
    public function createUserMadeReminder();
}
