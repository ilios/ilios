<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class ArchivableEntity
 */
trait ArchivableEntity
{
    /**
     * @return bool
     */
    public function isArchived(): bool
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
