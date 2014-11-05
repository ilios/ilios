<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Course
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="course")
 */
class Course implements CourseInterface
{
//    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="course_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $courseId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="course_level")
     */
    protected $level;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     */
    protected $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=18, name="external_id")
     */
    protected $externalName;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $locked;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $archived;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="published_as_tbd")
     */
    protected $publishedAsTbd;

    /**
     * @var CourseClerkshipTypeInterface
     *
     * @ORM\ManyToOne(targetEntity="CourseClerkshipType", inversedBy="courses")
     * @ORM\JoinColumn(name="clerkship_type_id", referencedColumnName="course_clerkship_type_id")
     */
    protected $clerkshipType;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="courses")
     * @ORM\JoinColumn(name="owning_school_id", referencedColumnName="school_id")
     */
    protected $school;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent", inversedBy="courses")
     * @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     */
    protected $publishEvent;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(
     *      name="course_directors",
     *      joinColumns={@ORM\JoinColumn(name="course_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id")}
     * )
     */
    protected $directors;

    /**
     * @var ArrayCollection|CohortInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Cohort", inversedBy="courses")
     * @ORM\JoinTable(
     *      name="course_x_cohort",
     *      joinColumns={@ORM\JoinColumn(name="course_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="cohort_id")}
     * )
     */
    protected $cohorts;

    /**
     * @var ArrayCollection|DisciplineInterface[]

     * @ORM\ManyToMany(targetEntity="Discipline", inversedBy="courses")
     * @ORM\JoinTable(
     *      name="course_x_discipline",
     *      joinColumns={@ORM\JoinColumn(name="course_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="discipline_id")}
     * )
     */
    protected $disciplines;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="courses")
     * @ORM\JoinTable(
     *      name="course_x_objective",
     *      joinColumns={@ORM\JoinColumn(name="course_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="objective_id")}
     * )
     */
    protected $objectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courses")
     * @ORM\JoinTable(
     *      name="course_x_mesh",
     *      joinColumns={@ORM\JoinColumn(name="course_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mesh_descriptor_uid")}
     * )
     */
    protected $meshDescriptors;

    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     *
     * @ORM\OneToMany(
     *      targetEntity="CourseLearningMaterial",
     *      mappedBy="course"
     * )
     */
    protected $courseLearningMaterials;

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
        $this->courseLearningMaterials = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->courseId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->courseId : $this->id;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
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

    /**
     * @param Collection $courseLearningMaterials
     */
    public function setLearningMaterials(Collection $courseLearningMaterials)
    {
        $this->courseLearningMaterials = new ArrayCollection();

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        $this->courseLearningMaterials->add($courseLearningMaterial);
    }

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }
}
