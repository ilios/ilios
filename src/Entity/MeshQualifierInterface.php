<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
    NameableEntityInterface,
    CreatedAtEntityInterface
{
    public function setDescriptors(Collection $descriptors);

    public function addDescriptor(MeshDescriptorInterface $descriptor);

    public function removeDescriptor(MeshDescriptorInterface $descriptor);

    public function getDescriptors(): Collection;
}
