<?php

namespace Ilios\CoreBundle\Traits;

/**
 * interface BlameableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface BlameableEntityInterface
{
    /**
     * @param  string $createdBy
     */
    public function setCreatedBy($createdBy);

    /**
     * Returns createdBy.
     *
     * @return string
     */
    public function getCreatedBy();

    /**
     * @param  string $updatedBy
     */
    public function setUpdatedBy($updatedBy);

    /**
     * @return string
     */
    public function getUpdatedBy();
}
