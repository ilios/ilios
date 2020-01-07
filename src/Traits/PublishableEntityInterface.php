<?php

namespace App\Traits;

/**
 * Interface PublishableEntityInterface
 */
interface PublishableEntityInterface
{
    /**
     * @return bool
     */
    public function isPublished();

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
    public function isPublishedAsTbd();
}
