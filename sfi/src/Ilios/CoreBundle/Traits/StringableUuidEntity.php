<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class StinrgableIdEntity
 * @package Ilios\CoreBundle\Traits
 */
trait StringableUuidEntity
{
    /**
    * @return string
    */
    public function __toString()
    {
        return (string) $this->uuid;
    }
}
