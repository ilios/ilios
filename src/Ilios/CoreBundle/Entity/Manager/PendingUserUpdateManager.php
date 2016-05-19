<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PendingUserUpdateManager extends BaseManager implements PendingUserUpdateManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findPendingUserUpdateBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findPendingUserUpdatesBy(
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
    public function updatePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($pendingUserUpdate);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($pendingUserUpdate));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deletePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate
    ) {
        $this->em->remove($pendingUserUpdate);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createPendingUserUpdate()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllPendingUserUpdates()
    {
        return $this->getRepository()->removeAllPendingUserUpdates();
    }
}
