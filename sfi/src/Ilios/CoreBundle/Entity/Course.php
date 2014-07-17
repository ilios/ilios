<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Course
 */
class Course
{
    /**
     * @var integer
     */
    private $courseId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var integer
     */
    private $courseLevel;

    /**
     * @var integer
     */
    private $year;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var string
     */
    private $externalId;

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
     * @var \Ilios\CoreBundle\Entity\CourseClerkshipType
     */
    private $clerkshipType;
    
    /**
     * @var \Ilios\CoreBundle\Entity\School
     */
    private $owningSchool;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $directors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cohorts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $disciplines;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $objectives;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meshDescriptors;
    
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
        $this->cohorts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->disciplines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objectives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meshDescriptors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get courseId
     *
     * @return integer 
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Course
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
     * Set courseLevel
     *
     * @param integer $courseLevel
     * @return Course
     */
    public function setCourseLevel($courseLevel)
    {
        $this->courseLevel = $courseLevel;

        return $this;
    }

    /**
     * Get courseLevel
     *
     * @return integer 
     */
    public function getCourseLevel()
    {
        return $this->courseLevel;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Course
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Course
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Course
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Course
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
     * Set externalId
     *
     * @param string $externalId
     * @return Course
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get externalId
     *
     * @return string 
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Course
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
     * @return Course
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
     * @return Course
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
     * Set clerkshipType
     *
     * @param \Ilios\CoreBundle\Entity\CourseClerkshipType $clerkshipType
     * @return Course
     */
    public function setClerkshipType(\Ilios\CoreBundle\Entity\CourseClerkshipType $clerkshipType = null)
    {
        $this->clerkshipType = $clerkshipType;

        return $this;
    }

    /**
     * Get clerkshipType
     *
     * @return \Ilios\CoreBundle\Entity\CourseClerkshipType 
     */
    public function getClerkshipType()
    {
        return $this->clerkshipType;
    }

    /**
     * Set owningSchool
     *
     * @param \Ilios\CoreBundle\Entity\School $school
     * @return ProgramYearSteward
     */
    public function setOwningSchool(\Ilios\CoreBundle\Entity\School $school = null)
    {
        $this->owningSchool = $school;

        return $this;
    }

    /**
     * Get owningSchool
     *
     * @return \Ilios\CoreBundle\Entity\School 
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * Add directors
     *
     * @param \Ilios\CoreBundle\Entity\User $directors
     * @return Course
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
     * Add cohorts
     *
     * @param \Ilios\CoreBundle\Entity\Cohort $cohorts
     * @return Course
     */
    public function addCohort(\Ilios\CoreBundle\Entity\Cohort $cohorts)
    {
        $this->cohorts[] = $cohorts;

        return $this;
    }

    /**
     * Remove cohorts
     *
     * @param \Ilios\CoreBundle\Entity\Cohort $cohorts
     */
    public function removeCohort(\Ilios\CoreBundle\Entity\Cohort $cohorts)
    {
        $this->cohorts->removeElement($cohorts);
    }

    /**
     * Get cohorts
     *
     * @return \Ilios\CoreBundle\Entity\Cohort[]
     */
    public function getCohorts()
    {
        return $this->cohorts->toArray();
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Entity\Discipline $disciplines
     * @return Course
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
     * @return Course
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
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     * @return Course
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Entity\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     * @return Course
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
