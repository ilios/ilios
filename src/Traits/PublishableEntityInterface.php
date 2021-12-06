<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface PublishableEntityInterface
 */
interface PublishableEntityInterface
{
    /**
     * @return bool
     */
    public function isPublished(): bool;

    /**
     * @param bool $published
     */
    public function setPublished($published);

    /**
     * @param bool $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd);

    /**
     * @return bool
     */
    public function isPublishedAsTbd(): bool;
}
