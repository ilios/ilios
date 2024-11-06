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
    public const int BLOCK = 1;
    public const int LONGITUDINAL = 2;
    public const int INTEGRATED = 3;
}
