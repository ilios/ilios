<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialUserRoleManager extends BaseManager implements LearningMaterialUserRoleManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findLearningMaterialUserRoleBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findLearningMaterialUserRolesBy(
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
    public function updateLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learningMaterialUserRole);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learningMaterialUserRole));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole
    ) {
        $this->em->remove($learningMaterialUserRole);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createLearningMaterialUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
