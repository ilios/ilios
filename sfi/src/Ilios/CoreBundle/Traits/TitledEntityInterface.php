<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface TitledEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface TitledEntityInterface
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
