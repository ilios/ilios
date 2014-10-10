<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class TitleTrait
 * @package Ilios\CoreBundle\Traits
 */
trait TitleTrait
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
