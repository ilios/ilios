<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface TitledEntityInterface
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
