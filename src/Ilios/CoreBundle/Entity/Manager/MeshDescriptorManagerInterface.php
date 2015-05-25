<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface MeshDescriptorManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     *
     * @return void
     */
    public function deleteMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return MeshDescriptorInterface
     */
    public function createMeshDescriptor();
}
