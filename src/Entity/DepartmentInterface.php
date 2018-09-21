<?php

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StewardedEntityInterface;

/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    StewardedEntityInterface
{

}
