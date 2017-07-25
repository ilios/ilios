<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LearningMaterialsEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

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
