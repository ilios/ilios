<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshTreeXDescriptorInterface;

/**
 * Interface MeshTreeXDescriptorManagerInterface
 */
interface MeshTreeXDescriptorManagerInterface
{
    /** 
     *@return MeshTreeXDescriptorInterface
     */
    public function createMeshTreeXDescriptor();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshTreeXDescriptorInterface
     */
    public function findMeshTreeXDescriptorBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTreeXDescriptorInterface[]|Collection
     */
    public function findMeshTreeXDescriptorsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshTreeXDescriptorInterface $meshTreeXDescriptor
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshTreeXDescriptor(MeshTreeXDescriptorInterface $meshTreeXDescriptor, $andFlush = true);

    /**
     * @param MeshTreeXDescriptorInterface $meshTreeXDescriptor
     *
     * @return void
     */
    public function deleteMeshTreeXDescriptor(MeshTreeXDescriptorInterface $meshTreeXDescriptor);

    /**
     * @return string
     */
    public function getClass();
}
