<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Interface UserRoleInterface
 */
interface UserRoleInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    RoleInterface,
    LoggableEntityInterface,
    UsersEntityInterface
{
}
