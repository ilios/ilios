<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\LearningMaterialsEntityInterface;
use App\Entity\TitledEntityInterface;

interface LearningMaterialStatusInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    LearningMaterialsEntityInterface
{
    public const int IN_DRAFT = 1;
    public const int FINALIZED  = 2;
    public const int REVISED = 3;
}
