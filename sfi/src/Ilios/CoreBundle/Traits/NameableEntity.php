<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NameableEntity
 * @package Ilios\CoreBundle\Traits
 */
trait NameableEntity
{
    /**
     * @ORM\Column(type="string", length=200)
     *
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
