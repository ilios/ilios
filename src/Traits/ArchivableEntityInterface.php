<?php

declare(strict_types=1);

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
