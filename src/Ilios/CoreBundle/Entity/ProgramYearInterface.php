<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\ArchivableEntityInterface;
use Ilios\CoreBundle\Traits\CategorizableEntityInterface;
use Ilios\CoreBundle\Traits\CompetenciesEntityInterface;
use Ilios\CoreBundle\Traits\DirectorsEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LockableEntityInterface;
use Ilios\CoreBundle\Traits\ObjectivesEntityInterface;
use Ilios\CoreBundle\Traits\PublishableEntityInterface;
use Ilios\CoreBundle\Traits\StewardedEntityInterface;

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
