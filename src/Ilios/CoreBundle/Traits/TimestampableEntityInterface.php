<?php

namespace Ilios\CoreBundle\Traits;

/**
 * interface TimestampableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface TimestampableEntityInterface
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param  \DateTime $updatedAt
     */
    public function stampUpdate();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
