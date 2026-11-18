<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\LearnerGroupsEntityInterface;
use App\Entity\TitledEntityInterface;
use App\Entity\CoursesEntityInterface;
use App\Entity\UsersEntityInterface;

interface CohortInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface,
    LearnerGroupsEntityInterface,
    UsersEntityInterface
{
    public function setProgramYear(?ProgramYearInterface $programYear = null): void;
    public function getProgramYear(): ?ProgramYearInterface;

    /**
     * Get the school we belong to
     */
    public function getSchool(): ?SchoolInterface;

    /**
     * Gets the program that this cohort belongs to.
     */
    public function getProgram(): ?ProgramInterface;
}
