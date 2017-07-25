<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\ActivatableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Ilios\CoreBundle\Entity\AamcPcrsInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Traits\ObjectivesEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;

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
