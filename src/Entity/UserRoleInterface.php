<?php

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\UsersEntityInterface;

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
