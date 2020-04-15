<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\LoggableEntityInterface;
use App\Entity\ObjectiveInterface;

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
