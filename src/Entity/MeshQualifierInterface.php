<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CreatedAtEntityInterface;
use App\Entity\IdentifiableStringEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Entity\NameableEntityInterface;
use App\Entity\TimestampableEntityInterface;

interface MeshQualifierInterface extends
    IdentifiableStringEntityInterface,
    TimestampableEntityInterface,
    NameableEntityInterface,
    CreatedAtEntityInterface
{
    public function setDescriptors(Collection $descriptors): void;
    public function addDescriptor(MeshDescriptorInterface $descriptor): void;
    public function removeDescriptor(MeshDescriptorInterface $descriptor): void;
    public function getDescriptors(): Collection;
}
