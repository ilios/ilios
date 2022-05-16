<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\UsersEntityInterface;

interface CohortInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface,
    LearnerGroupsEntityInterface,
    UsersEntityInterface
{
    public function setProgramYear(ProgramYearInterface $programYear = null);
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
