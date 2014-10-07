<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class NameableTrait
 * @package Ilios\CoreBundle\Traits
 */
trait NameableTrait
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
