<?php

namespace Ilios\CoreBundle\Traits;

/**
 * interface TimestampableTraitinterface
 * @package Ilios\CoreBundle\Traits
 */
interface TimestampableTraitinterface
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
