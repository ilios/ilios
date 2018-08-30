<?php

namespace AppBundle\Traits;

/**
 * Interface NameableEntityInterface
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
