<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class DepartmentManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class DepartmentManager extends AbstractManager implements DepartmentManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findDepartmentBy(
        array $criteria,
        array $orderBy = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findDepartmentsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateDepartment(
        DepartmentInterface $department,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($department);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($department));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDepartment(
        DepartmentInterface $department
    ) {
        $department->setDeleted(true);
        $this->updateDepartment($department);
    }

    /**
     * {@inheritdoc}
     */
    public function createDepartment()
    {
        $class = $this->getClass();
        return new $class();
    }
}
