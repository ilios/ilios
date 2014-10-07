<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class IdentifiableTrait
 * @package Ilios\CoreBundle\Traits
 */
trait IdentifiableTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
