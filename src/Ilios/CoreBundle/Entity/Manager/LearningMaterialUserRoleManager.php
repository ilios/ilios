<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialUserRoleManager extends AbstractManager implements LearningMaterialUserRoleManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialUserRoleInterface
     */
    public function findLearningMaterialUserRoleBy(
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
     * @return ArrayCollection|LearningMaterialUserRoleInterface[]
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
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     */
    public function deleteLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole
    ) {
        $this->em->remove($learningMaterialUserRole);
        $this->em->flush();
    }

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function createLearningMaterialUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
