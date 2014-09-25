<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorManagerInterface
 */
interface MeshDescriptorManagerInterface
{
    /** 
     *@return MeshDescriptorInterface
     */
    public function createMeshDescriptor();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]|Collection
     */
    public function findMeshDescriptorsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshDescriptor(MeshDescriptorInterface $meshDescriptor, $andFlush = true);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     *
     * @return void
     */
    public function deleteMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
     * @return string
     */
    public function getClass();
}
