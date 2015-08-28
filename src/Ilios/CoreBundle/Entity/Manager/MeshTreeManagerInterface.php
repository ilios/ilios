<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * Interface MeshTreeManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface MeshTreeManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshTreeInterface
     */
    public function findMeshTreeBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTreeInterface[]|Collection
     */
    public function findMeshTreesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshTreeInterface $meshTree
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateMeshTree(MeshTreeInterface $meshTree, $andFlush = true);

    /**
     * @param MeshTreeInterface $meshTree
     *
     * @return void
     */
    public function deleteMeshTree(MeshTreeInterface $meshTree);

    /**
     * @return MeshTreeInterface
     */
    public function createMeshTree();
}
