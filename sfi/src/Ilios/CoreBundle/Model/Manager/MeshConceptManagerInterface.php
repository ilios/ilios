<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshConceptInterface;

/**
 * Interface MeshConceptManagerInterface
 */
interface MeshConceptManagerInterface
{
    /** 
     *@return MeshConceptInterface
     */
    public function createMeshConcept();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshConceptInterface
     */
    public function findMeshConceptBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshConceptInterface[]|Collection
     */
    public function findMeshConceptsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshConceptInterface $meshConcept
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshConcept(MeshConceptInterface $meshConcept, $andFlush = true);

    /**
     * @param MeshConceptInterface $meshConcept
     *
     * @return void
     */
    public function deleteMeshConcept(MeshConceptInterface $meshConcept);

    /**
     * @return string
     */
    public function getClass();
}
