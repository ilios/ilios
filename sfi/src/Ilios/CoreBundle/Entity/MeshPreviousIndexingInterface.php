<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Interface MeshPreviousIndexingInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshPreviousIndexingInterface
{
    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function setDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @return MeshDescriptorInterface
     */
    public function getDescriptor();

    /**
     * @param string $previousIndexing
     */
    public function setPreviousIndexing($previousIndexing);

    /**
     * @return string
     */
    public function getPreviousIndexing();
}
