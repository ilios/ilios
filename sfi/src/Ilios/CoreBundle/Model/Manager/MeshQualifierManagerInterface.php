<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshQualifierInterface;

/**
 * Interface MeshQualifierManagerInterface
 */
interface MeshQualifierManagerInterface
{
    /** 
     *@return MeshQualifierInterface
     */
    public function createMeshQualifier();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshQualifierInterface
     */
    public function findMeshQualifierBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return MeshQualifierInterface[]|Collection
     */
    public function findMeshQualifiersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshQualifier(MeshQualifierInterface $meshQualifier, $andFlush = true);

    /**
     * @param MeshQualifierInterface $meshQualifier
     *
     * @return void
     */
    public function deleteMeshQualifier(MeshQualifierInterface $meshQualifier);

    /**
     * @return string
     */
    public function getClass();
}
