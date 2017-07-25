<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface ArchivableEntityInterface
 */
interface ArchivableEntityInterface
{
    /**
     * @return boolean
     */
    public function isArchived();

    /**
     * @param boolean $archived
     */
    public function setArchived($archived);
}
