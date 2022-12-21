<?php

declare(strict_types=1);

namespace App\Traits;

trait TitledNullableEntity
{
    protected ?string $title = null;

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}
