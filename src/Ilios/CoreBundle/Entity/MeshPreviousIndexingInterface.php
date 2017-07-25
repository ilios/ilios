<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Interface MeshPreviousIndexingInterface
 */
interface MeshPreviousIndexingInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface
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
