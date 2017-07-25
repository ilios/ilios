<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;

/**
 * Interface CourseClerkshipTypeInterface
 */
interface CourseClerkshipTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface
{
    /**
     * @var int
     */
    const BLOCK = 1;
    /**
     * @var int
     */
    const LONGITUDINAL = 2;
    /**
     * @var int
     */
    const INTEGRATED = 3;
}
