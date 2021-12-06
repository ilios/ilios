<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class PublishableEntity
 */
trait PublishableEntity
{
    /**
     * @param bool $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = (bool) $publishedAsTbd;
    }

    /**
     * @return bool
     */
    public function isPublishedAsTbd(): bool
    {
        return $this->publishedAsTbd;
    }
}
