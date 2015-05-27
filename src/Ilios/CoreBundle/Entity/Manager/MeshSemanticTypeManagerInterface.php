<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Interface MeshSemanticTypeManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|MeshSemanticTypeInterface[]
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
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType,
        $andFlush = true,
        $forceId  = false
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
