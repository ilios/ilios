<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface UniversallyUniqueEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface UniversallyUniqueEntityInterface
{
    /**
     * @param string $uuid
     */
    public function setUuid($uuid);

    /**
     * @return string
     */
    public function getUuid();
}
