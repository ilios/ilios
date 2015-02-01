<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Trait UniversallyUniqueEntity
 * @package Ilios\CoreBundle\Traits
 */
trait UniversallyUniqueEntity
{
    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}
