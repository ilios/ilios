<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;

interface MeshTreeInterface extends
    StringableEntityInterface,
    IdentifiableEntityInterface
{
    public function setTreeNumber(string $treeNumber);
    public function getTreeNumber(): string;

    public function setDescriptor(MeshDescriptorInterface $descriptor): MeshTree;
    public function getDescriptor(): MeshDescriptorInterface;
}
