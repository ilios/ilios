<?php

namespace AppBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Entity\ProgramInterface;

/**
 * Interface ProgramsEntityInterface
 */
interface ProgramsEntityInterface
{
    /**
     * @param Collection $programs
     */
    public function setPrograms(Collection $programs);

    /**
     * @param ProgramInterface $program
     */
    public function addProgram(ProgramInterface $program);

    /**
     * @param ProgramInterface $program
     */
    public function removeProgram(ProgramInterface $program);

    /**
    * @return ProgramInterface[]|ArrayCollection
    */
    public function getPrograms();
}
