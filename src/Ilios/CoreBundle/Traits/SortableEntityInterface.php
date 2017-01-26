<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface SortableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface SortableEntityInterface
{
    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();
}
