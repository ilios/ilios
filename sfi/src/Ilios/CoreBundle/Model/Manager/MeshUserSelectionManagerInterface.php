<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshUserSelectionInterface;

/**
 * Interface MeshUserSelectionManagerInterface
 */
interface MeshUserSelectionManagerInterface
{
    /** 
     *@return MeshUserSelectionInterface
     */
    public function createMeshUserSelection();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshUserSelectionInterface
     */
    public function findMeshUserSelectionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshUserSelectionInterface[]|Collection
     */
    public function findMeshUserSelectionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshUserSelection(MeshUserSelectionInterface $meshUserSelection, $andFlush = true);

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     *
     * @return void
     */
    public function deleteMeshUserSelection(MeshUserSelectionInterface $meshUserSelection);

    /**
     * @return string
     */
    public function getClass();
}
