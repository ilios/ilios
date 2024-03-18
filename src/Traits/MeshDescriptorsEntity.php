<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\MeshDescriptorInterface;

/**
 * Class MeshDescriptorsEntity
 */
trait MeshDescriptorsEntity
{
    protected Collection $meshDescriptors;

    public function setMeshDescriptors(Collection $meshDescriptors): void
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }

    public function getMeshDescriptors(): Collection
    {
        return $this->meshDescriptors;
    }
}
