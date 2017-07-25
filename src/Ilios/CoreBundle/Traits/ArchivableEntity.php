<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Class ArchivableEntity
 */
trait ArchivableEntity
{
    /**
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param boolean $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }
}
