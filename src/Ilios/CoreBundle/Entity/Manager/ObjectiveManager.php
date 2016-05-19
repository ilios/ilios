<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class ObjectiveManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ObjectiveManager extends BaseManager implements ObjectiveManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findObjectiveBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findObjectivesBy(
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
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($objective);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($objective));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteObjective(
        ObjectiveInterface $objective
    ) {
        $this->em->remove($objective);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createObjective()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalObjectiveCount()
    {
        return $this->em->createQuery('SELECT COUNT(o.id) FROM IliosCoreBundle:Objective o')->getSingleScalarResult();
    }
}
