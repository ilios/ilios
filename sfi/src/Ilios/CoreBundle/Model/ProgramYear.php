<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramYear
 */
class ProgramYear
{
    /**
     * @var int
     */
    protected $programYearId;

    /**
     * @var int
     */
    protected $startYear;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var boolean
     */
    protected $archived;

    /**
     * @var boolean
     */
    protected $publishedAsTbd;

    /**
     * @var \Ilios\CoreBundle\Model\Program
     */
    protected $program;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $directors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $competencies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $disciplines;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $objectives;
    
    /**
     * @var \Ilios\CoreBundle\Model\PublishEvent
     */
    protected $publishEvent;

    protected $cohorts;

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
     * @return int 
     */
    public function getProgramYearId()
    {
        return $this->programYearId;
    }

    /**
     * Set startYear
     *
     * @param int $startYear
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
     * @return int 
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
     * @param \Ilios\CoreBundle\Model\Program $program
     * @return ProgramYear
     */
    public function setProgram(\Ilios\CoreBundle\Model\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return \Ilios\CoreBundle\Model\Program 
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Add directors
     *
     * @param \Ilios\CoreBundle\Model\User $directors
     * @return ProgramYear
     */
    public function addDirector(\Ilios\CoreBundle\Model\User $directors)
    {
        $this->directors[] = $directors;

        return $this;
    }

    /**
     * Remove directors
     *
     * @param \Ilios\CoreBundle\Model\User $directors
     */
    public function removeDirector(\Ilios\CoreBundle\Model\User $directors)
    {
        $this->directors->removeElement($directors);
    }

    /**
     * Get directors
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getDirectors()
    {
        return $this->directors->toArray();
    }

    /**
     * Add competencies
     *
     * @param \Ilios\CoreBundle\Model\Competency $competencies
     * @return ProgramYear
     */
    public function addCompetency(\Ilios\CoreBundle\Model\Competency $competencies)
    {
        $this->competencies[] = $competencies;

        return $this;
    }

    /**
     * Remove competencies
     *
     * @param \Ilios\CoreBundle\Model\Competency $competencies
     */
    public function removeCompetency(\Ilios\CoreBundle\Model\Competency $competencies)
    {
        $this->competencies->removeElement($competencies);
    }

    /**
     * Get competencies
     *
     * @return \Ilios\CoreBundle\Model\Competency[]
     */
    public function getCompetencies()
    {
        return $this->competencies->toArray();
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Model\Discipline $disciplines
     * @return ProgramYear
     */
    public function addDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines)
    {
        $this->disciplines[] = $disciplines;

        return $this;
    }

    /**
     * Remove disciplines
     *
     * @param \Ilios\CoreBundle\Model\Discipline $disciplines
     */
    public function removeDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines)
    {
        $this->disciplines->removeElement($disciplines);
    }

    /**
     * Get disciplines
     *
     * @return \Ilios\CoreBundle\Model\Discipline[]
     */
    public function getDisciplines()
    {
        return $this->disciplines->toArray();
    }

    /**
     * Add objectives
     *
     * @param \Ilios\CoreBundle\Model\Objective $objectives
     * @return ProgramYear
     */
    public function addObjective(\Ilios\CoreBundle\Model\Objective $objectives)
    {
        $this->objectives[] = $objectives;

        return $this;
    }

    /**
     * Remove objectives
     *
     * @param \Ilios\CoreBundle\Model\Objective $objectives
     */
    public function removeObjective(\Ilios\CoreBundle\Model\Objective $objectives)
    {
        $this->objectives->removeElement($objectives);
    }

    /**
     * Get objectives
     *
     * @return \Ilios\CoreBundle\Model\Objective[]
     */
    public function getObjectives()
    {
        return $this->objectives->toArray();
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Model\PublishEvent $publishEvent
     * @return ProgramYear
     */
    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;

        return $this;
    }

    /**
     * Get publishEvent
     *
     * @return \Ilios\CoreBundle\Model\PublishEvent 
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
