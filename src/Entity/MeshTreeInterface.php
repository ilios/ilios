<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface MeshTreeInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setTreeNumber(string $treeNumber);
    public function getTreeNumber(): string;

    public function setDescriptor(MeshDescriptorInterface $descriptor): MeshTree;
    public function getDescriptor(): MeshDescriptorInterface;
}
