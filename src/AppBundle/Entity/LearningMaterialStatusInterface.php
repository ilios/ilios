<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\LearningMaterialsEntityInterface;
use AppBundle\Traits\TitledEntityInterface;

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
    const IN_DRAFT = 1;

    /**
     * @var int
     */
    const FINALIZED  = 2;

    /**
     * @var int
     */
    const REVISED = 3;
}
