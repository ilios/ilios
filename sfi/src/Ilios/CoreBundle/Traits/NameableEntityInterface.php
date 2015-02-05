<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface NameableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface NameableEntityInterface
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
