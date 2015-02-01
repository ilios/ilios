<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface MeshSemanticTypeInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshSemanticTypeInterface extends
    UniversallyUniqueEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface
{
}

