<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\DeletableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    DeletableEntityInterface
{

}
