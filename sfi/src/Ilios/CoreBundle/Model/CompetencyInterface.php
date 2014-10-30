<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Ilios\CoreBundle\Model\AamcPcrsInterface;
use Ilios\CoreBundle\Model\SchoolInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface CompetencyInterface
 * @package Ilios\CoreBundle\Model
 */
interface CompetencyInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();

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
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function getAamcPcrses();

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear);

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears();
}

