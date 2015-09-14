<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PendingUserUpdateManager extends AbstractManager implements PendingUserUpdateManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PendingUserUpdateInterface
     */
    public function findPendingUserUpdateBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|PendingUserUpdateInterface[]
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
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
    public function deletePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate
    ) {
        $this->em->remove($pendingUserUpdate);
        $this->em->flush();
    }

    /**
     * @return PendingUserUpdateInterface
     */
    public function createPendingUserUpdate()
    {
        $class = $this->getClass();
        return new $class();
    }
    
    /**
     * @inheritdoc
     */
    public function removeAllPendingUserUpdates()
    {
        return $this->getRepository()->removeAllPendingUserUpdates();
    }
}
