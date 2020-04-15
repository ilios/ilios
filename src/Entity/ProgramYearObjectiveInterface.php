<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ObjectiveRelationshipInterface;

/**
 * Interface ProgramYearObjectiveInterface
 */
interface ProgramYearObjectiveInterface extends
    ObjectiveRelationshipInterface
{
    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear): void;

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear(): ProgramYearInterface;
}
