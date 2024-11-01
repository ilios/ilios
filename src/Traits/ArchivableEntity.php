<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class ArchivableEntity
 */
trait ArchivableEntity
{
    protected bool $archived;

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
}
