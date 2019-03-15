<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\ArchivableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\LockableEntityInterface;
use App\Traits\ObjectivesEntityInterface;
use App\Traits\PublishableEntityInterface;
use App\Traits\StewardedEntityInterface;

/**
 * Interface ProgramYearInterface
 */
interface ProgramYearInterface extends
    IdentifiableEntityInterface,
    LockableEntityInterface,
    ArchivableEntityInterface,
    LoggableEntityInterface,
    StewardedEntityInterface,
    ObjectivesEntityInterface,
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

    /**
     * @param ProgramInterface $program
     */
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

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();
}
