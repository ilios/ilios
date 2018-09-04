<?php

namespace AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Entity\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorsEntityInterface
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
