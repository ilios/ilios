<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Entity\CompetencyInterface;
use App\Traits\StringableEntityToIdInterface;
use App\Traits\IdentifiableEntityInterface;

/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface extends
    IdentifiableEntityInterface,
    StringableEntityToIdInterface,
    LoggableEntityInterface,
    CompetenciesEntityInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    public function getDescription(): string;
}
