<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DescribableEntity
 * @package Ilios\CoreBundle\Traits
 */
trait DescribableEntity
{
    /**
     * @ORM\Column(type="text")
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
