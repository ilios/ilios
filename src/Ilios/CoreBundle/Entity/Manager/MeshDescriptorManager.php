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
}
