<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshTermInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshTermInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface
{
    /**
     *
     * @param boolean $printable
     */
    public function setPrintable($printable);

    /**
     * @return boolean
     */
    public function isPrintable();
}
