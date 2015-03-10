<?php

namespace Ilios\CoreBundle\Traits;

/**
 * TimestampableEntity
 * @package Ilios\CoreBundle\Traits
 */
trait TimestampableEntity
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param  \DateTime $updatedAt
     */
    public function stampUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
