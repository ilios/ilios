<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorsEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface MeshDescriptorsEntityInterface
{
    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
    * @return MeshDescriptorInterface[]|ArrayCollection
    */
    public function getMeshDescriptors();
}
