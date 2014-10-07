<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface NameableTraitInterface
 * @package Ilios\CoreBundle\Traits
 */
interface NameableTraitInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
}
