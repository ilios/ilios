<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface TitleTraitInterface
 * @package Ilios\CoreBundle\Traits
 */
interface TitleTraitInterface
{
    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();
}
