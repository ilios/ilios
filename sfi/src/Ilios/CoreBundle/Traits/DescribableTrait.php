<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class DescribableTrait
 * @package Ilios\CoreBundle\Traits
 */
trait DescribableTrait
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
