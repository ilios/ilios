<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\MeshDescriptorInterface;

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
