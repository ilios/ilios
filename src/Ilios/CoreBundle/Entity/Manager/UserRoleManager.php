<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class UserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserRoleManager extends AbstractManager implements UserRoleManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserRoleInterface
     */
    public function findUserRoleBy(
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
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function findUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserRoleInterface $userRole
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateUserRole(
        UserRoleInterface $userRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($userRole);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($userRole));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserRoleInterface $userRole
     */
    public function deleteUserRole(
        UserRoleInterface $userRole
    ) {
        $this->em->remove($userRole);
        $this->em->flush();
    }

    /**
     * @return UserRoleInterface
     */
    public function createUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
