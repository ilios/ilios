<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Class ProgramsEntity
 * @package Ilios\CoreBundle\Traits
 */
trait ProgramsEntity
{
    /**
     * @param Collection $programs
     */
    public function setPrograms(Collection $programs)
    {
        $this->programs = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addProgram($program);
        }
    }

    /**
     * @param ProgramInterface $program
     */
    public function addProgram(ProgramInterface $program)
    {
        $this->programs->add($program);
    }

    /**
    * @return ProgramInterface[]|ArrayCollection
    */
    public function getPrograms()
    {
        //criteria not 100% reliable on many to many relationships
        //fix in https://github.com/doctrine/doctrine2/pull/1399
        // $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", false));
        // return new ArrayCollection($this->programs->matching($criteria)->getValues());
        
        $arr = $this->programs->filter(function ($entity) {
            return !$entity->isDeleted();
        })->toArray();
        
        $reIndexed = array_values($arr);
        
        return new ArrayCollection($reIndexed);
    }
}
