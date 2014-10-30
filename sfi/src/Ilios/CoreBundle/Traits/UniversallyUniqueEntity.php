<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UniversallyUniqueEntity
 * @package Ilios\CoreBundle\Traits
 */
trait UniversallyUniqueEntity
{
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $uuid;

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
