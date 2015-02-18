<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * Interface MeshUserSelectionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface MeshUserSelectionManagerInterface
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
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshUserSelectionInterface[]|Collection
     */
    public function findMeshUserSelectionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection,
        $andFlush = true
    );

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     *
     * @return void
     */
    public function deleteMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return MeshUserSelectionInterface
     */
    public function createMeshUserSelection();
}
