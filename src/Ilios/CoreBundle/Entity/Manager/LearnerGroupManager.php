<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearnerGroupManager extends AbstractManager implements LearnerGroupManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findLearnerGroupBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findLearnerGroupDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findLearnerGroupDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteLearnerGroup(
        LearnerGroupInterface $learnerGroup
    ) {
        $this->em->remove($learnerGroup);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createLearnerGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
