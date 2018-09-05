<?php

namespace AppBundle\Traits;

/**
 * Class SortableEntity
 */
trait SortableEntity
{
    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
