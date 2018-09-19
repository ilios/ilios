<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use App\Traits\CompetenciesEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Entity\CompetencyInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

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
