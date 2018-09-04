<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\ArchivableEntityInterface;
use AppBundle\Traits\CategorizableEntityInterface;
use AppBundle\Traits\CompetenciesEntityInterface;
use AppBundle\Traits\DirectorsEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\LockableEntityInterface;
use AppBundle\Traits\ObjectivesEntityInterface;
use AppBundle\Traits\PublishableEntityInterface;
use AppBundle\Traits\StewardedEntityInterface;

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
    PublishableEntityInterface,
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
