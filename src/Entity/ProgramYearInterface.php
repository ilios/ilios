<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ArchivableEntityInterface;
use App\Entity\CategorizableEntityInterface;
use App\Entity\CompetenciesEntityInterface;
use App\Entity\DirectorsEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\LockableEntityInterface;
use App\Entity\ProgramYearObjectivesEntityInterface;

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
