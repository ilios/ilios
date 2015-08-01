<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Interface MeshQualifierManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface MeshQualifierManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshQualifierInterface
     */
    public function findMeshQualifierBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshQualifierInterface[]
     */
    public function findMeshQualifiersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshQualifier(
        MeshQualifierInterface $meshQualifier,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param MeshQualifierInterface $meshQualifier
     *
     * @return void
     */
    public function deleteMeshQualifier(
        MeshQualifierInterface $meshQualifier
    );

    /**
     * @return MeshQualifierInterface
     */
    public function createMeshQualifier();
}
