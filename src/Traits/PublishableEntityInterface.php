<?php

declare(strict_types=1);

namespace App\Traits;

interface PublishableEntityInterface
{
    public function isPublished(): bool;
    public function setPublished(bool $published): void;

    public function setPublishedAsTbd(bool $publishedAsTbd): void;
    public function isPublishedAsTbd(): bool;
}
