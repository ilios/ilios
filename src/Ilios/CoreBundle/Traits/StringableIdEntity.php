<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class StringableIdEntity
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
