<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshPreviousIndexingManager extends BaseManager implements MeshPreviousIndexingManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshPreviousIndexingBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshPreviousIndexingsBy(
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
    public function updateMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshPreviousIndexing);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshPreviousIndexing));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing
    ) {
        $this->em->remove($meshPreviousIndexing);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshPreviousIndexing()
    {
        $class = $this->getClass();
        return new $class();
    }
}
