<?php

declare(strict_types=1);

namespace App\Traits;

interface TitledEntityInterface
{
    public function setTitle(string $title): void;
    public function getTitle(): string;
}
