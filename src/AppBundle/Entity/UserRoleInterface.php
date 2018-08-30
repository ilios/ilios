<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\UsersEntityInterface;
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
