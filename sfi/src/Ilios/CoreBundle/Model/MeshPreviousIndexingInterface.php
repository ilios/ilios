<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface MeshPreviousIndexingInterface
 * @package Ilios\CoreBundle\Model
 */
interface MeshPreviousIndexingInterface extends UniversallyUniqueEntityInterface
{
    /**
     * @param string $previousIndexing
     */
    public function setPreviousIndexing($previousIndexing);

    /**
     * @return string
     */
    public function getPreviousIndexing();
}
