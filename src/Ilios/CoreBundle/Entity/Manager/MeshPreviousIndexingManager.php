<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshPreviousIndexingManager extends AbstractManager implements MeshPreviousIndexingManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshPreviousIndexingInterface
     */
    public function findMeshPreviousIndexingBy(
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
     * @return ArrayCollection|MeshPreviousIndexingInterface[]
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
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     */
    public function deleteMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing
    ) {
        $this->em->remove($meshPreviousIndexing);
        $this->em->flush();
    }

    /**
     * @return MeshPreviousIndexingInterface
     */
    public function createMeshPreviousIndexing()
    {
        $class = $this->getClass();
        return new $class();
    }
}
