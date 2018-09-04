<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\StewardedEntityInterface;

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
