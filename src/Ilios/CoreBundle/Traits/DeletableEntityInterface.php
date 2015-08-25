<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface DeletableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface DeletableEntityInterface
{
    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();
}
