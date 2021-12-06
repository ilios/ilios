<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class TitledEntity
 */
trait TitledEntity
{
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
    public function getTitle(): string
    {
        return $this->title;
    }
}
