<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LearningMaterialsEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

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
