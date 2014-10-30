<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Course
 */
class Course implements CourseInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @var integer
     */
    protected $year;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @TODO: Talk with Sascha about this.
     * @var string
     */
    protected $externalName;

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
     * @var CourseClerkshipTypeInterface
     */
    protected $clerkshipType;

    /**
     * @var SchoolInterface
     */
    protected $school;

    /**
     * @var PublishEventInterface
     */
    protected $publishEvent;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    protected $directors;

    /**
     * @var ArrayCollection|CohortInterface[]
     */
    protected $cohorts;

    /**
     * @var ArrayCollection|DisciplineInterface[]
     */
    protected $disciplines;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     */
    protected $objectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     */
    protected $meshDescriptors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->directors = new ArrayCollection();
        $this->cohorts = new ArrayCollection();
        $this->disciplines = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
    }

    /**
     * @param integer $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @todo: Possible rename.
     * @param string $externalName
     */
    public function setExternalName($externalName)
    {
        $this->externalName = $externalName;
    }

    /**
     * @todo: Possible rename.
     * @return string
     */
    public function getExternalName()
    {
        return $this->externalName;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param boolean $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    /**
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    /**
     * @return boolean
     */
    public function isPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }

    /**
     * @param CourseClerkshipTypeInterface $clerkshipType
     */
    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType)
    {
        $this->clerkshipType = $clerkshipType;
    }

    /**
     * @return \Ilios\CoreBundle\Model\CourseClerkshipType
     */
    public function getClerkshipType()
    {
        return $this->clerkshipType;
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
     * @param Collection|UserInterface[] $directors
     */
    public function setDirectors(Collection $directors)
    {
        $this->directors = new ArrayCollection();

        foreach ($directors as $director) {
            $this->addDirector($director);
        }
    }

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director)
    {
        $this->directors->add($director);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * @param Collection|CohortInterface[] $cohorts
     */
    public function setCohorts(Collection $cohorts)
    {
        $this->cohorts = new ArrayCollection();

        foreach ($cohorts as $cohort) {
            $this->addCohort($cohort);
        }
    }

    /**
     * @param CohortInterface $cohorts
     */
    public function addCohort(CohortInterface $cohorts)
    {
        $this->cohorts->add($cohorts);
    }

    /**
     * @return ArrayCollection|CohortInterface[]
     */
    public function getCohorts()
    {
        return $this->cohorts;
    }

    /**
     * @param DisciplineInterface $disciplines
     */
    public function addDiscipline(DisciplineInterface $disciplines)
    {
        $this->disciplines->add($disciplines);
    }

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines()
    {
        return $this->disciplines;
    }

    /**
     * @param Collection|ObjectiveInterface[] $objectives
     */
    public function setObjectives(Collection $objectives)
    {
        $this->objectives = new ArrayCollection();

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @param ObjectiveInterface $objectives
     */
    public function addObjective(ObjectiveInterface $objectives)
    {
        $this->objectives->add($objectives);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * @param Collection|MeshDescriptorInterface[] $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptors
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptors)
    {
        $this->meshDescriptors->add($meshDescriptors);
    }

    /**
     * @return Collection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors;
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent)
    {
        $this->publishEvent = $publishEvent;
    }

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }
}
