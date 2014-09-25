<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshTermInterface;

/**
 * Interface MeshTermManagerInterface
 */
interface MeshTermManagerInterface
{
    /** 
     *@return MeshTermInterface
     */
    public function createMeshTerm();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshTermInterface
     */
    public function findMeshTermBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTermInterface[]|Collection
     */
    public function findMeshTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshTermInterface $meshTerm
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshTerm(MeshTermInterface $meshTerm, $andFlush = true);

    /**
     * @param MeshTermInterface $meshTerm
     *
     * @return void
     */
    public function deleteMeshTerm(MeshTermInterface $meshTerm);

    /**
     * @return string
     */
    public function getClass();
}
