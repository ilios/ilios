<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class StinrgableIdEntity
 * @package Ilios\CoreBundle\Traits
 */
trait StringableIdEntity
{
    /**
    * @return string
    */
    public function __toString()
    {
        return (string) $this->id;

    }
}
