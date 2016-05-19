<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class UserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserRoleManager extends BaseManager implements UserRoleManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findUserRoleBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserRolesBy(
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
     * {@inheritdoc}
     */
    public function deleteUserRole(
        UserRoleInterface $userRole
    ) {
        $this->em->remove($userRole);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
