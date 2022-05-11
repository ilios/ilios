<?php

declare(strict_types=1);

namespace App\Traits;

interface PublishableEntityInterface
{
    public function isPublished(): bool;
    public function setPublished(bool $published);

    public function setPublishedAsTbd(bool $publishedAsTbd);
    public function isPublishedAsTbd(): bool;
}
