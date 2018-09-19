<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshQualifierInterface
 */
interface MeshQualifierInterface extends
    IdentifiableEntityInterface,
    TimestampableEntityInterface,
    NameableEntityInterface
{

    /**
     * @param Collection $descriptors
     */
    public function setDescriptors(Collection $descriptors);

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function addDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function removeDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getDescriptors();
}
