<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\TitledEntityInterface;

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
