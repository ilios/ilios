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
    public const BLOCK = 1;
    /**
     * @var int
     */
    public const LONGITUDINAL = 2;
    /**
     * @var int
     */
    public const INTEGRATED = 3;
}
