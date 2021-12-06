<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\ProgramYearsEntityInterface;

/**
 * Interface CompetencyInterface
 */
interface CompetencyInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    ActivatableEntityInterface,
    ProgramYearObjectivesEntityInterface
{
    public function setParent(CompetencyInterface $parent);

    public function getParent(): CompetencyInterface;

    public function setChildren(Collection $children);

    public function addChild(CompetencyInterface $child);

    public function removeChild(CompetencyInterface $child);

    public function getChildren(): Collection;

    public function hasChildren(): bool;

    public function setAamcPcrses(Collection $aamcPcrses);

    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs);

    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs);

    public function getAamcPcrses(): Collection;
}
