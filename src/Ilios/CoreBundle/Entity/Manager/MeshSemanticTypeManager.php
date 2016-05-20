<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class MeshSemanticTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 * @deprecated
 */
class MeshSemanticTypeManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshSemanticTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshSemanticTypesBy(
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
    public function updateMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($meshSemanticType, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType
    ) {
        $this->delete($meshSemanticType);
    }

    /**
     * @deprecated
     */
    public function createMeshSemanticType()
    {
        return $this->create();
    }
}
