<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface MeshTermInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshTermInterface extends
    UniversallyUniqueEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface
{
}
