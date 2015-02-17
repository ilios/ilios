<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Interface MeshSemanticTypeManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface MeshSemanticTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshSemanticTypeInterface
     */
    public function findMeshSemanticTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshSemanticTypeInterface[]|Collection
     */
    public function findMeshSemanticTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType,
        $andFlush = true
    );

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     *
     * @return void
     */
    public function deleteMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return MeshSemanticTypeInterface
     */
    public function createMeshSemanticType();
}
