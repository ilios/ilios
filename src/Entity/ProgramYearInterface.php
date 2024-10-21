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
    public function setStartYear(int $startYear): void;
    public function getStartYear(): int;

    public function setProgram(ProgramInterface $program): void;
    public function getProgram(): ProgramInterface;

    /**
     * Gets the school that this program year belongs to.
     */
    public function getSchool(): SchoolInterface;

    public function setCohort(CohortInterface $cohort): void;
    public function getCohort(): ?CohortInterface;
}
