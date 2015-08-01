<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Class PermissionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PermissionManager extends AbstractManager implements PermissionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PermissionInterface
     */
    public function findPermissionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|PermissionInterface[]
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($permission);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($permission));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PermissionInterface $permission
     */
    public function deletePermission(
        PermissionInterface $permission
    ) {
        $this->em->remove($permission);
        $this->em->flush();
    }

    /**
     * @return PermissionInterface
     */
    public function createPermission()
    {
        $class = $this->getClass();
        return new $class();
    }
}
