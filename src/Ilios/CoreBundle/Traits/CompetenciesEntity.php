<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetenciesEntity
 */
trait CompetenciesEntity
{
    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies)
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency)
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
        }
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function removeCompetency(CompetencyInterface $competency)
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
        }
    }

    /**
    * @return CompetencyInterface[]|ArrayCollection
    */
    public function getCompetencies()
    {
        return $this->competencies;
    }
}
