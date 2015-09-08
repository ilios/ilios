<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearnerGroupManager extends AbstractManager implements LearnerGroupManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearnerGroupInterface
     */
    public function findLearnerGroupBy(
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
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function findLearnerGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLearnerGroup(
        LearnerGroupInterface $learnerGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learnerGroup);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learnerGroup));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function deleteLearnerGroup(
        LearnerGroupInterface $learnerGroup
    ) {
        $this->em->remove($learnerGroup);
        $this->em->flush();
    }

    /**
     * @return LearnerGroupInterface
     */
    public function createLearnerGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
