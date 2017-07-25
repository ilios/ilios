<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Interface ProgramYearsEntityInterface
 */
interface ProgramYearsEntityInterface
{
    /**
     * @param Collection $programYears
     */
    public function setProgramYears(Collection $programYears);

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear);

    /**
     * @param ProgramYearInterface $programYear
     */
    public function removeProgramYear(ProgramYearInterface $programYear);

    /**
    * @return ProgramYearInterface[]|ArrayCollection
    */
    public function getProgramYears();
}
