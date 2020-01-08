<?php

namespace App\Traits;

/**
 * Class ArchivableEntity
 */
trait ArchivableEntity
{
    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }
}
