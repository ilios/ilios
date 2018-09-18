<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\UsersEntityInterface;

/**
 * Interface UserRoleInterface
 */
interface UserRoleInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    UsersEntityInterface
{
}
