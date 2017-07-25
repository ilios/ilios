<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class PublishableEntity
 */
trait PublishableEntity
{
    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = (boolean) $publishedAsTbd;
    }

    /**
     * @return boolean
     */
    public function isPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }
}
