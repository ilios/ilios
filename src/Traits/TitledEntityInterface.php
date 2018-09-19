<?php

namespace App\Traits;

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
