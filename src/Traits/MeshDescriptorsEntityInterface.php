<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorsEntityInterface
 */
interface MeshDescriptorsEntityInterface
{
    public function setMeshDescriptors(Collection $meshDescriptors);

    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
    * @return MeshDescriptorInterface[]|ArrayCollection
    */
    public function getMeshDescriptors(): Collection;
}
