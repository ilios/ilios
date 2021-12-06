<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface ArchivableEntityInterface
 */
interface ArchivableEntityInterface
{
    public function isArchived(): bool;

    /**
     * @param bool $archived
     */
    public function setArchived($archived);
}
