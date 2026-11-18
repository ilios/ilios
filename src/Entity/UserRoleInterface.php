<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\TitledEntityInterface;
use App\Entity\UsersEntityInterface;

interface UserRoleInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    UsersEntityInterface
{
}
