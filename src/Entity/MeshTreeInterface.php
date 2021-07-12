<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;

/**
 * Class MeshTreeInterface
 */
interface MeshTreeInterface extends
    StringableEntityInterface,
    IdentifiableEntityInterface
{

    /**
     * Set treeNumber
     *
     * @param string $treeNumber
     */
    public function setTreeNumber($treeNumber);

    /**
     * Get treeNumber
     *
     * @return string
     */
    public function getTreeNumber();

    /**
     * Set meshDescriptor
     *
     * @param MeshDescriptorInterface $descriptor
     *
     * @return MeshTree
     */
    public function setDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * Get meshDescriptor
     *
     * @return MeshDescriptorInterface
     */
    public function getDescriptor();
}
