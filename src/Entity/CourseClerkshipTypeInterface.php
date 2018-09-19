<?php

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\CoursesEntityInterface;

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
