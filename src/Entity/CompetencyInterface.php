<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ProgramYearObjectivesEntityInterface;
use App\Entity\TitledNullableEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Entity\ActivatableEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\SchoolEntityInterface;
use App\Entity\ProgramYearsEntityInterface;

interface CompetencyInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    ActivatableEntityInterface,
    ProgramYearObjectivesEntityInterface
{
    public function setParent(?CompetencyInterface $parent = null): void;
    public function getParent(): ?CompetencyInterface;

    public function setChildren(Collection $children): void;
    public function addChild(CompetencyInterface $child): void;
    public function removeChild(CompetencyInterface $child): void;

    public function getChildren(): Collection;
    public function hasChildren(): bool;

    public function setAamcPcrses(Collection $aamcPcrses): void;
    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs): void;
    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs): void;
    public function getAamcPcrses(): Collection;
}
