<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\TitledEntityInterface;

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
