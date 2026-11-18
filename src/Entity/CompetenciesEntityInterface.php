<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CompetencyInterface;
use Doctrine\Common\Collections\Collection;

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
