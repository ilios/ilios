<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Interface MeshConceptManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface MeshConceptManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshConceptInterface
     */
    public function findMeshConceptBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function findMeshConceptsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshConceptInterface $meshConcept
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshConcept(
        MeshConceptInterface $meshConcept,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param MeshConceptInterface $meshConcept
     *
     * @return void
     */
    public function deleteMeshConcept(
        MeshConceptInterface $meshConcept
    );

    /**
     * @return MeshConceptInterface
     */
    public function createMeshConcept();
}
