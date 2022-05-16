<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityToIdInterface;

interface MeshPreviousIndexingInterface extends
    IdentifiableEntityInterface,
    StringableEntityToIdInterface
{
    public function setDescriptor(MeshDescriptorInterface $descriptor);
    public function getDescriptor(): MeshDescriptorInterface;

    public function setPreviousIndexing(string $previousIndexing);
    public function getPreviousIndexing(): string;
}
