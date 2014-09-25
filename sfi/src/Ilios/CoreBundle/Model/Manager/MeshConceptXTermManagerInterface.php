<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshConceptXTermInterface;

/**
 * Interface MeshConceptXTermManagerInterface
 */
interface MeshConceptXTermManagerInterface
{
    /** 
     *@return MeshConceptXTermInterface
     */
    public function createMeshConceptXTerm();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshConceptXTermInterface
     */
    public function findMeshConceptXTermBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshConceptXTermInterface[]|Collection
     */
    public function findMeshConceptXTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshConceptXTermInterface $meshConceptXTerm
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshConceptXTerm(MeshConceptXTermInterface $meshConceptXTerm, $andFlush = true);

    /**
     * @param MeshConceptXTermInterface $meshConceptXTerm
     *
     * @return void
     */
    public function deleteMeshConceptXTerm(MeshConceptXTermInterface $meshConceptXTerm);

    /**
     * @return string
     */
    public function getClass();
}
