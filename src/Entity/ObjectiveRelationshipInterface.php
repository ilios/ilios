<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SortableEntityInterface;

/**
 * Interface ObjectiveRelationshipInterface
 */
interface ObjectiveRelationshipInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface
{
    /**
     * @param ObjectiveInterface $objective
     */
    public function setObjective(ObjectiveInterface $objective): void;

    /**
     * @return ObjectiveInterface
     */
    public function getObjective(): ObjectiveInterface;
}
