<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competency
 */
class Competency
{
    /**
     * @var integer
     */
    private $competencyId;

    /**
     * @var string
     */
    private $title;
    
    /**
     * @var \Ilios\CoreBundle\Model\School
     */
    private $owningSchool;

    /**
     * @var \Ilios\CoreBundle\Model\Competency
     */
    private $parentCompetency;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pcrses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $programYears;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pcrses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->programYears = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get competencyId
     *
     * @return integer 
     */
    public function getCompetencyId()
    {
        return $this->competencyId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Competency
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set owningSchool
     *
     * @param \Ilios\CoreBundle\Model\School $school
     * @return ProgramYearSteward
     */
    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null)
    {
        $this->owningSchool = $school;

        return $this;
    }

    /**
     * Get owningSchool
     *
     * @return \Ilios\CoreBundle\Model\School 
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * Set parentCompetency
     *
     * @param \Ilios\CoreBundle\Model\Competency $parentCompetency
     * @return Competency
     */
    public function setParentCompetency(\Ilios\CoreBundle\Model\Competency $parentCompetency = null)
    {
        $this->parentCompetency = $parentCompetency;

        return $this;
    }

    /**
     * Get parentCompetency
     *
     * @return \Ilios\CoreBundle\Model\Competency 
     */
    public function getParentCompetency()
    {
        return $this->parentCompetency;
    }

    /**
     * Add pcrses
     *
     * @param \Ilios\CoreBundle\Model\AamcPcrs $pcrses
     * @return Competency
     */
    public function addPcrs(\Ilios\CoreBundle\Model\AamcPcrs $pcrses)
    {
        $this->pcrses[] = $pcrses;

        return $this;
    }

    /**
     * Remove pcrses
     *
     * @param \Ilios\CoreBundle\Model\AamcPcrs $pcrses
     */
    public function removePcrs(\Ilios\CoreBundle\Model\AamcPcrs $pcrses)
    {
        $this->pcrses->removeElement($pcrses);
    }

    /**
     * Get pcrses
     *
     * @return \Ilios\CoreBundle\Model\AamcPcrs[]
     */
    public function getPcrses()
    {
        return $this->pcrses->toArray();
    }

    /**
     * Add programYears
     *
     * @param \Ilios\CoreBundle\Model\ProgramYear $programYears
     * @return Competency
     */
    public function addProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param \Ilios\CoreBundle\Model\ProgramYear $programYears
     */
    public function removeProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return \Ilios\CoreBundle\Model\ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }
}
