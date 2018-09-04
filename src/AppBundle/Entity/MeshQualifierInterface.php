<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Traits\TimestampableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;

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
