<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Traits\CompetenciesEntityInterface;
use AppBundle\Traits\DescribableEntityInterface;
use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Entity\CompetencyInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
}
