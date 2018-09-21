<?php

namespace App\Traits;

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
