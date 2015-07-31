<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * Class MeshUserSelectionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshUserSelectionManager extends AbstractManager implements MeshUserSelectionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshUserSelectionInterface
     */
    public function findMeshUserSelectionBy(
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
     * @return ArrayCollection|MeshUserSelectionInterface[]
     */
    public function findMeshUserSelectionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshUserSelection);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshUserSelection));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     */
    public function deleteMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection
    ) {
        $this->em->remove($meshUserSelection);
        $this->em->flush();
    }

    /**
     * @return MeshUserSelectionInterface
     */
    public function createMeshUserSelection()
    {
        $class = $this->getClass();
        return new $class();
    }
}
