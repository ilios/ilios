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
    public function setCompetencies(Collection $competencies): void;

    public function addCompetency(CompetencyInterface $competency): void;

    public function removeCompetency(CompetencyInterface $competency): void;

    public function getCompetencies(): Collection;
}
