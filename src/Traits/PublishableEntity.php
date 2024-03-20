<?php

declare(strict_types=1);

namespace App\Traits;

trait PublishableEntity
{
    protected bool $published;
    protected bool $publishedAsTbd;

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublishedAsTbd(bool $publishedAsTbd): void
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    public function isPublishedAsTbd(): bool
    {
        return $this->publishedAsTbd;
    }
}
