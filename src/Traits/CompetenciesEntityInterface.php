<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CompetencyInterface;

/**
 * Interface CompetenciesEntityInterface
 */
interface CompetenciesEntityInterface
{
    public function setCompetencies(Collection $competencies);

    public function addCompetency(CompetencyInterface $competency);

    public function removeCompetency(CompetencyInterface $competency);

    /**
    * @return CompetencyInterface[]|ArrayCollection
    */
    public function getCompetencies();
}
