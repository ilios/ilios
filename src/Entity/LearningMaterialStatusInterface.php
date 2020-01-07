<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialStatusInterface
 */
interface LearningMaterialStatusInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    LearningMaterialsEntityInterface
{
    /**
     * @var int
     */
    public const IN_DRAFT = 1;

    /**
     * @var int
     */
    public const FINALIZED  = 2;

    /**
     * @var int
     */
    public const REVISED = 3;
}
