<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\CoursesEntityInterface;

interface CourseClerkshipTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface
{
    public const BLOCK = 1;
    public const LONGITUDINAL = 2;
    public const INTEGRATED = 3;
}
