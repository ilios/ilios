<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Interface CourseClerkshipTypeInterface
 * @package Ilios\CoreBundle\Entity
 */
interface CourseClerkshipTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface
{
}
