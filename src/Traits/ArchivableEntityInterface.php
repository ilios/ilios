<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface ArchivableEntityInterface
 */
interface ArchivableEntityInterface
{
    public function isArchived(): bool;

    public function setArchived(bool $archived): void;
}
