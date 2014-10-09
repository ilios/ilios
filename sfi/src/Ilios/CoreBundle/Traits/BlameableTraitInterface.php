<?php

namespace Ilios\CoreBundle\Traits;

/**
 * interface BlameableTraitInterface
 * @package Ilios\CoreBundle\Traits
 */
interface BlameableTraitInterface
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
