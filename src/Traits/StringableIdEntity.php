<?php

declare(strict_types=1);

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
