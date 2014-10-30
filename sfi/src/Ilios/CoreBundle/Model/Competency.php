<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

use Ilios\CoreBundle\Model\CompetencyInterface;
use Ilios\CoreBundle\Model\AamcPcrsInterface;
use Ilios\CoreBundle\Model\SchoolInterface;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Competency
 * @package Ilios\CoreBundle\Model
 */
class Competency implements CompetencyInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var SchoolInterface
     */
    protected $school;

    /**
     * @var CompetencyInterface
     */
    protected $parent;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     */
    protected $children;

    /**
     * @var ArrayCollection|AamcPcrsInterface[]
     */
    protected $aamcPcrses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     */
    protected $programYears;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aamcPcrses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param CompetencyInterface $parent
     */
    public function setParent(CompetencyInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return CompetencyInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param CompetencyInterface $child
     */
    public function addChild(CompetencyInterface $child)
    {
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return (!$this->children->isEmpty()) ? true : false;
    }

    /**
     * @param Collection $aamcPcrses
     */
    public function setAamcPcrses(Collection $aamcPcrses)
    {
        $this->aamcPcrses = new ArrayCollection();

        foreach ($aamcPcrses as $aamcPcrs) {
            $this->addAamcPcrs($aamcPcrs);
        }
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     */
    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs)
    {
        $this->aamcPcrses->add($aamcPcrs);
    }

    /**
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function getAamcPcrses()
    {
        return $this->aamcPcrses;
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->add($programYear);
    }

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears()
    {
        return $this->programYears;
    }
}
