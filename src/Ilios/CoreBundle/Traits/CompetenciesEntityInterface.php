<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Interface CompetenciesEntityInterface
 */
interface CompetenciesEntityInterface
{
    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies);

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency);

    /**
     * @param CompetencyInterface $competency
     */
    public function removeCompetency(CompetencyInterface $competency);

    /**
    * @return CompetencyInterface[]|ArrayCollection
    */
    public function getCompetencies();
}
