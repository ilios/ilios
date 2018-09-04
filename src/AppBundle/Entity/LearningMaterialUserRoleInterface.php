<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\LearningMaterialsEntityInterface;
use AppBundle\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialUserRoleInterface
 */
interface LearningMaterialUserRoleInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    LearningMaterialsEntityInterface
{
}
