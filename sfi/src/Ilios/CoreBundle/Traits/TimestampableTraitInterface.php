<?php

namespace Ilios\CoreBundle\Traits;

/**
 * interface TimestampableEntityinterface
 * @package Ilios\CoreBundle\Traits
 */
interface TimestampableEntityinterface
{
    /**
     * @param  \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param  \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
