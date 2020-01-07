<?php

namespace App\Traits;

/**
 * Interface ArchivableEntityInterface
 */
interface ArchivableEntityInterface
{
    /**
     * @return bool
     */
    public function isArchived();

    /**
     * @param bool $archived
     */
    public function setArchived($archived);
}
