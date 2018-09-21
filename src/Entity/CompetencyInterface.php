<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

use App\Entity\AamcPcrsInterface;
use App\Entity\SchoolInterface;
use App\Entity\ProgramYearInterface;
use App\Traits\ObjectivesEntityInterface;
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
    ObjectivesEntityInterface
{
    /**
     * @param CompetencyInterface $parent
     */
    public function setParent(CompetencyInterface $parent);

    /**
     * @return CompetencyInterface
     */
    public function getParent();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param CompetencyInterface $child
     */
    public function addChild(CompetencyInterface $child);

    /**
     * @param CompetencyInterface $child
     */
    public function removeChild(CompetencyInterface $child);

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param Collection $aamcPcrses
     */
    public function setAamcPcrses(Collection $aamcPcrses);

    /**
     * @param AamcPcrsInterface $aamcPcrs
     */
    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs);

    /**
     * @param AamcPcrsInterface $aamcPcrs
     */
    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs);

    /**
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function getAamcPcrses();
}
