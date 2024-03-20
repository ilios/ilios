<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface MeshPreviousIndexingInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setDescriptor(MeshDescriptorInterface $descriptor): void;
    public function getDescriptor(): MeshDescriptorInterface;

    public function setPreviousIndexing(string $previousIndexing): void;
    public function getPreviousIndexing(): string;
}
