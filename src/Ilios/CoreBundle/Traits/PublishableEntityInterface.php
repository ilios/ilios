<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface PublishableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface PublishableEntityInterface
{
    /**
     * @return boolean
     */
    public function isPublished();

    /**
     * @param boolean $published
     */
    public function setPublished($published);

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd);

    /**
     * @return boolean
     */
    public function isPublishedAsTbd();
}
