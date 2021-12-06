<?php

declare(strict_types=1);

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

    public function getTitle(): string;
}
