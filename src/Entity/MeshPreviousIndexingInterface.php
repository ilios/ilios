<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface MeshPreviousIndexingInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setDescriptor(MeshDescriptorInterface $descriptor);
    public function getDescriptor(): MeshDescriptorInterface;

    public function setPreviousIndexing(string $previousIndexing);
    public function getPreviousIndexing(): string;
}
