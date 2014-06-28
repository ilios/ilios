<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramYear
 */
class ProgramYear
{
    /**
     * @var integer
     */
    private $programYearId;

    /**
     * @var integer
     */
    private $startYear;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var boolean
     */
    private $locked;

    /**
     * @var boolean
     */
    private $archived;

    /**
     * @var boolean
     */
    private $publishedAsTbd;

    /**
     * @var \Ilios\CoreBundle\Entity\Program
     */
    private $program;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $directors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $competencies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $disciplines;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $objectives;
    
    /**
     * @var \Ilios\CoreBundle\Entity\PublishEvent
     */
    private $publishEvent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->directors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->competencies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->disciplines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objectives = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get programYearId
     *
     * @return integer 
     */
    public function getProgramYearId()
    {
        return $this->programYearId;
    }

    /**
     * Set startYear
     *
     * @param integer $startYear
     * @return ProgramYear
     */
    public function setStartYear($startYear)
    {
        $this->startYear = $startYear;

        return $this;
    }

    /**
     * Get startYear
     *
     * @return integer 
     */
    public function getStartYear()
    {
        return $this->startYear;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return ProgramYear
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return ProgramYear
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     * @return ProgramYear
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean 
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set publishedAsTbd
     *
     * @param boolean $publishedAsTbd
     * @return ProgramYear
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;

        return $this;
    }

    /**
     * Get publishedAsTbd
     *
     * @return boolean 
     */
    public function getPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }

    /**
     * Set program
     *
     * @param \Ilios\CoreBundle\Entity\Program $program
     * @return ProgramYear
     */
    public function setProgram(\Ilios\CoreBundle\Entity\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return \Ilios\CoreBundle\Entity\Program 
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Add directors
     *
     * @param \Ilios\CoreBundle\Entity\User $directors
     * @return ProgramYear
     */
    public function addDirector(\Ilios\CoreBundle\Entity\User $directors)
    {
        $this->directors[] = $directors;

        return $this;
    }

    /**
     * Remove directors
     *
     * @param \Ilios\CoreBundle\Entity\User $directors
     */
    public function removeDirector(\Ilios\CoreBundle\Entity\User $directors)
    {
        $this->directors->removeElement($directors);
    }

    /**
     * Get directors
     *
     * @return \Ilios\CoreBundle\Entity\User[]
     */
    public function getDirectors()
    {
        return $this->directors->toArray();
    }

    /**
     * Add competencies
     *
     * @param \Ilios\CoreBundle\Entity\Competency $competencies
     * @return ProgramYear
     */
    public function addCompetency(\Ilios\CoreBundle\Entity\Competency $competencies)
    {
        $this->competencies[] = $competencies;

        return $this;
    }

    /**
     * Remove competencies
     *
     * @param \Ilios\CoreBundle\Entity\Competency $competencies
     */
    public function removeCompetency(\Ilios\CoreBundle\Entity\Competency $competencies)
    {
        $this->competencies->removeElement($competencies);
    }

    /**
     * Get competencies
     *
     * @return \Ilios\CoreBundle\Entity\Competency[]
     */
    public function getCompetencies()
    {
        return $this->competencies->toArray();
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Entity\Discipline $disciplines
     * @return ProgramYear
     */
    public function addDiscipline(\Ilios\CoreBundle\Entity\Discipline $disciplines)
    {
        $this->disciplines[] = $disciplines;

        return $this;
    }

    /**
     * Remove disciplines
     *
     * @param \Ilios\CoreBundle\Entity\Discipline $disciplines
     */
    public function removeDiscipline(\Ilios\CoreBundle\Entity\Discipline $disciplines)
    {
        $this->disciplines->removeElement($disciplines);
    }

    /**
     * Get disciplines
     *
     * @return \Ilios\CoreBundle\Entity\Discipline[]
     */
    public function getDisciplines()
    {
        return $this->disciplines->toArray();
    }

    /**
     * Add objectives
     *
     * @param \Ilios\CoreBundle\Entity\Objective $objectives
     * @return ProgramYear
     */
    public function addObjective(\Ilios\CoreBundle\Entity\Objective $objectives)
    {
        $this->objectives[] = $objectives;

        return $this;
    }

    /**
     * Remove objectives
     *
     * @param \Ilios\CoreBundle\Entity\Objective $objectives
     */
    public function removeObjective(\Ilios\CoreBundle\Entity\Objective $objectives)
    {
        $this->objectives->removeElement($objectives);
    }

    /**
     * Get objectives
     *
     * @return \Ilios\CoreBundle\Entity\Objective[]
     */
    public function getObjectives()
    {
        return $this->objectives->toArray();
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     * @return ProgramYear
     */
    public function setPublishEvent(\Ilios\CoreBundle\Entity\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * Get publishEvent
     *
     * @return \Ilios\CoreBundle\Entity\PublishEvent 
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
