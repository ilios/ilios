<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\CoursesEntityInterface;

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
