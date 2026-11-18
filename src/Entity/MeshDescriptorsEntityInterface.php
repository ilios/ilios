<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\MeshDescriptorInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface MeshDescriptorsEntityInterface
 */
interface MeshDescriptorsEntityInterface
{
    public function setMeshDescriptors(Collection $meshDescriptors): void;

    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void;

    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void;

    public function getMeshDescriptors(): Collection;
}
