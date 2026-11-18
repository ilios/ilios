<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\LearningMaterialsEntityInterface;
use App\Entity\TitledEntityInterface;

interface LearningMaterialUserRoleInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    LearningMaterialsEntityInterface
{
}
