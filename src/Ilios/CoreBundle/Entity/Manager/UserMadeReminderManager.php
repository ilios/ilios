<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserMadeReminderManager extends BaseManager implements UserMadeReminderManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findUserMadeReminderBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserMadeRemindersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($userMadeReminder);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($userMadeReminder));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder
    ) {
        $this->em->remove($userMadeReminder);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createUserMadeReminder()
    {
        $class = $this->getClass();
        return new $class();
    }
}
