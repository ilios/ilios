<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\ObjectiveInterface;
use App\Entity\ObjectiveRelationshipInterface;

/**
 * Trait ObjectiveRelationshipEntity
 * @package App\Traits
 * @see ObjectiveRelationshipInterface
 */
trait ObjectiveRelationshipEntity
{
    use CategorizableEntity;
    use SortableEntity;

    /**
     * @param ObjectiveInterface $objective
     */
    public function setObjective(ObjectiveInterface $objective): void
    {
        $this->objective = $objective;
    }

    /**
     * @return ObjectiveInterface
     */
    public function getObjective(): ObjectiveInterface
    {
        return $this->objective;
    }
}
