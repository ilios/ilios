<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Course
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="course", indexes={
 *   @ORM\Index(name="title_course_k", columns={"course_id", "title"}),
 *     @ORM\Index(name="external_id", columns={"external_id"}),
 *     @ORM\Index(name="clerkship_type_id", columns={"clerkship_type_id"})
 * })
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CourseRepository")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Course implements CourseInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="course_level")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $level;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $year;

    /**
     * @var \DateTime
     * @todo: add a format and variable timezone if possible
     * @ORM\Column(type="date", name="start_date")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     */
    protected $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="deleted")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $deleted;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=18, name="external_id", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 18
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("externalId")
     */
    protected $externalId;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $locked;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $archived;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="published_as_tbd")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    protected $publishedAsTbd;

    /**
     * @var CourseClerkshipTypeInterface
     *
     * @ORM\ManyToOne(targetEntity="CourseClerkshipType", inversedBy="courses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clerkship_type_id", referencedColumnName="course_clerkship_type_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("clerkshipType")
     */
    protected $clerkshipType;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="courses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owning_school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("owningSchool")
     */
    protected $owningSchool;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent", inversedBy="courses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("publishEvent")
     */
    protected $publishEvent;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="directedCourses"))
     * @ORM\JoinTable(name="course_director",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $directors;

    /**
     * @var ArrayCollection|CohortInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Cohort", inversedBy="courses")
     * @ORM\JoinTable(name="course_x_cohort",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="cohort_id", referencedColumnName="cohort_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $cohorts;

    /**
     * @var ArrayCollection|DisciplineInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Discipline", inversedBy="courses")
     * @ORM\JoinTable(name="course_x_discipline",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="discipline_id", referencedColumnName="discipline_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $disciplines;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="courses")
     * @ORM\JoinTable(name="course_x_objective",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $objectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courses")
     * @ORM\JoinTable(name="course_x_mesh",
     *    joinColumns={
     *      @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *    },
     *    inverseJoinColumns={
     *      @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", onDelete="CASCADE")
     *    }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     */
    protected $meshDescriptors;

    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="CourseLearningMaterial",mappedBy="course")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learningMaterials")
     */
    protected $courseLearningMaterials;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="course")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

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
        $this->sessions = new ArrayCollection();
        $this->deleted = false;
        $this->publishedAsTbd = false;
        $this->archived = false;
        $this->locked = false;
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
    public function setStartDate(\DateTime $startDate = null)
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
    public function setEndDate(\DateTime $endDate = null)
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
        $this->deleted = (boolean) $deleted;
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
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @todo: Possible rename.
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = (boolean) $locked;
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
        $this->archived = (boolean) $archived;
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
        $this->publishedAsTbd = (boolean) $publishedAsTbd;
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
    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType = null)
    {
        $this->clerkshipType = $clerkshipType;
    }

    /**
     * @return CourseClerkshipTypeInterface
     */
    public function getClerkshipType()
    {
        return $this->clerkshipType;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setOwningSchool(SchoolInterface $school)
    {
        $this->owningSchool = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * @param Collection|UserInterface[] $directors
     */
    public function setDirectors(Collection $directors = null)
    {
        $this->directors = new ArrayCollection();
        if (is_null($directors)) {
            return;
        }

        foreach ($directors as $director) {
            $this->addDirector($director);
        }
    }

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director = null)
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
    public function setCohorts(Collection $cohorts = null)
    {
        $this->cohorts = new ArrayCollection();
        if (is_null($cohorts)) {
            return;
        }

        foreach ($cohorts as $cohort) {
            $this->addCohort($cohort);
        }
    }

    /**
     * @param CohortInterface $cohort
     */
    public function addCohort(CohortInterface $cohort)
    {
        $this->cohorts->add($cohort);
    }

    /**
     * @return ArrayCollection|CohortInterface[]
     */
    public function getCohorts()
    {
        return $this->cohorts;
    }

    /**
     * @param Collection|DisciplineInterface[] $disciplines
     */
    public function setDisciplines(Collection $disciplines = null)
    {
        $this->disciplines = new ArrayCollection();
        if (is_null($disciplines)) {
            return;
        }

        foreach ($disciplines as $discipline) {
            $this->addDiscipline($discipline);
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline)
    {
        $this->disciplines->add($discipline);
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
    public function setObjectives(Collection $objectives = null)
    {
        $this->objectives = new ArrayCollection();
        if (is_null($objectives)) {
            return;
        }

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        $this->objectives->add($objective);
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
    public function setMeshDescriptors(Collection $meshDescriptors = null)
    {
        $this->meshDescriptors = new ArrayCollection();
        if (is_null($meshDescriptors)) {
            return;
        }

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->add($meshDescriptor);
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
    public function setPublishEvent(PublishEventInterface $publishEvent = null)
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
    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null)
    {
        $this->courseLearningMaterials = new ArrayCollection();
        if (is_null($courseLearningMaterials)) {
            return;
        }

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

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions = null)
    {
        $this->sessions = new ArrayCollection();
        if (is_null($sessions)) {
            return;
        }
        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
