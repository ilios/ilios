<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface IdentifiableTraitIntertface
 * @package Ilios\CoreBundle\Traits
 */
interface IdentifiableTraitIntertface
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();
}
