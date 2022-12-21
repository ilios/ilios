<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;

interface MeshPreviousIndexingInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface
{
    public function setDescriptor(MeshDescriptorInterface $descriptor);
    public function getDescriptor(): MeshDescriptorInterface;

    public function setPreviousIndexing(string $previousIndexing);
    public function getPreviousIndexing(): string;
}
