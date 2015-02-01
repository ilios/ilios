<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface MeshQualifierInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshQualifierInterface extends
    UniversallyUniqueEntityInterface,
    TimestampableEntityInterface,
    NameableEntityInterface
{
    //^ Lol
}
