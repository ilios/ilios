<?php

namespace Ilios\CoreBundle\Model;

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
     * @var \Ilios\CoreBundle\Model\CourseClerkshipType
     */
    private $clerkshipType;
    
    /**
     * @var \Ilios\CoreBundle\Model\School
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
     * @var \Ilios\CoreBundle\Model\PublishEvent
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
     * @param \Ilios\CoreBundle\Model\CourseClerkshipType $clerkshipType
     * @return Course
     */
    public function setClerkshipType(\Ilios\CoreBundle\Model\CourseClerkshipType $clerkshipType = null)
    {
        $this->clerkshipType = $clerkshipType;

        return $this;
    }

    /**
     * Get clerkshipType
     *
     * @return \Ilios\CoreBundle\Model\CourseClerkshipType 
     */
    public function getClerkshipType()
    {
        return $this->clerkshipType;
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
     * Add directors
     *
     * @param \Ilios\CoreBundle\Model\User $directors
     * @return Course
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
     * Add cohorts
     *
     * @param \Ilios\CoreBundle\Model\Cohort $cohorts
     * @return Course
     */
    public function addCohort(\Ilios\CoreBundle\Model\Cohort $cohorts)
    {
        $this->cohorts[] = $cohorts;

        return $this;
    }

    /**
     * Remove cohorts
     *
     * @param \Ilios\CoreBundle\Model\Cohort $cohorts
     */
    public function removeCohort(\Ilios\CoreBundle\Model\Cohort $cohorts)
    {
        $this->cohorts->removeElement($cohorts);
    }

    /**
     * Get cohorts
     *
     * @return \Ilios\CoreBundle\Model\Cohort[]
     */
    public function getCohorts()
    {
        return $this->cohorts->toArray();
    }

    /**
     * Add disciplines
     *
     * @param \Ilios\CoreBundle\Model\Discipline $disciplines
     * @return Course
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
     * @return Course
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
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     * @return Course
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Model\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }

    /**
     * Set publishEvent
     *
     * @param \Ilios\CoreBundle\Model\PublishEvent $publishEvent
     * @return Course
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
