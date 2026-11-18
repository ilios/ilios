<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Interface ArchivableEntityInterface
 */
interface ArchivableEntityInterface
{
    public function isArchived(): bool;

    public function setArchived(bool $archived): void;
}
