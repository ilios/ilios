<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\TitledNullableEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\ProgramYearsEntityInterface;

interface CompetencyInterface extends
    IdentifiableEntityInterface,
    TitledNullableEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    ActivatableEntityInterface,
    ProgramYearObjectivesEntityInterface
{
    public function setParent(CompetencyInterface $parent): void;
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
