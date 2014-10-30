<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class TitledEntity
 * @package Ilios\CoreBundle\Traits
 */
trait TitledEntity
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
