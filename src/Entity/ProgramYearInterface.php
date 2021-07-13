<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\ArchivableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\LockableEntityInterface;

/**
 * Interface ProgramYearInterface
 */
interface ProgramYearInterface extends
    IdentifiableEntityInterface,
    LockableEntityInterface,
    ArchivableEntityInterface,
    LoggableEntityInterface,
    ProgramYearObjectivesEntityInterface,
    CategorizableEntityInterface,
    DirectorsEntityInterface,
    CompetenciesEntityInterface
{
    /**
     * @param int $startYear
     */
    public function setStartYear($startYear);

    /**
     * @return int
     */
    public function getStartYear();

    public function setProgram(ProgramInterface $program);

    /**
     * @return ProgramInterface
     */
    public function getProgram();

    /**
     * Gets the school that this program year belongs to.
     * @return SchoolInterface|null
     */
    public function getSchool();

    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();
}
