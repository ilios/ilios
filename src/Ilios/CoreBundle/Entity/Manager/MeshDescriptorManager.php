<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository;

/**
 * Class MeshDescriptorManager
 */
class MeshDescriptorManager extends BaseManager
{
    /**
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * @see MeshDescriptorRepository::clearExistingData()
     */
    public function clearExistingData()
    {
        $this->getRepository()->clearExistingData();
    }

    /**
     * @param array $data
     * @param array $existingDescriptorIds
     * @see MeshDescriptorRepository::upsertMeshUniverse()
     */
    public function upsertMeshUniverse(array $data, array $existingDescriptorIds)
    {
        $this->getRepository()->upsertMeshUniverse($data, $existingDescriptorIds);
    }

    /**
     * @param array $meshDescriptors
     * @see MeshDescriptorRepository::flagDescriptorsAsDeleted()
     */
    public function flagDescriptorsAsDeleted(array $meshDescriptors)
    {
        $this->getRepository()->flagDescriptorsAsDeleted($meshDescriptors);
    }
}
