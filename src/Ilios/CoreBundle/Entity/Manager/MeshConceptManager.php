<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshConceptManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshConceptBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshConceptsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateMeshConcept(
        MeshConceptInterface $meshConcept,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($meshConcept, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshConcept(
        MeshConceptInterface $meshConcept
    ) {
        $this->delete($meshConcept);
    }

    /**
     * @deprecated
     */
    public function createMeshConcept()
    {
        return $this->create();
    }
}
