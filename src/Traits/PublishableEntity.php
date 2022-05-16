<?php

declare(strict_types=1);

namespace App\Traits;

trait PublishableEntity
{
    public function setPublished(bool $published)
    {
        $this->published = $published;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublishedAsTbd(bool $publishedAsTbd)
    {
        $this->publishedAsTbd = (bool) $publishedAsTbd;
    }

    public function isPublishedAsTbd(): bool
    {
        return $this->publishedAsTbd;
    }
}
