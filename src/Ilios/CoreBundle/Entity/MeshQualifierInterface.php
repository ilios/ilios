<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

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
