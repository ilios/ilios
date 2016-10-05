<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\CategorizableEntity;
use Ilios\CoreBundle\Traits\CohortsEntity;
use Ilios\CoreBundle\Traits\DirectorsEntity;
use Ilios\CoreBundle\Traits\MeshDescriptorsEntity;
use Ilios\CoreBundle\Traits\ObjectivesEntity;
use Ilios\CoreBundle\Traits\PublishableEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\ArchivableEntity;
use Ilios\CoreBundle\Traits\LockableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

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
 * @JMS\AccessType("public_method")
 */
class Course implements CourseInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LockableEntity;
    use ArchivableEntity;
    use SessionsEntity;
    use SchoolEntity;
    use ObjectivesEntity;
    use PublishableEntity;
    use CategorizableEntity;
    use CohortsEntity;
    use MeshDescriptorsEntity;
    use DirectorsEntity;

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
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="external_id", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
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
    protected $published;

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
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="courses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("school")
     */
    protected $school;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="directedCourses"))
     * @ORM\JoinTable(name="course_director",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
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
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="courses")
     * @ORM\JoinTable(name="course_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $terms;

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
    protected $learningMaterials;

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
     * @var CourseInterface
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="course_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $ancestor;

    /**
     * @var CourseInterface
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="ancestor")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $descendants;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->directors = new ArrayCollection();
        $this->cohorts = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->learningMaterials = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->descendants = new ArrayCollection();
        $this->publishedAsTbd = false;
        $this->published = false;
        $this->archived = false;
        $this->locked = false;
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritdoc
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @inheritdoc
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @inheritdoc
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @inheritdoc
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @inheritdoc
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @inheritdoc
     */
    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType = null)
    {
        $this->clerkshipType = $clerkshipType;
    }

    /**
     * @inheritdoc
     */
    public function getClerkshipType()
    {
        return $this->clerkshipType;
    }
    
    /**
     * @inheritdoc
     */
    public function setLearningMaterials(Collection $learningMaterials = null)
    {
        $this->learningMaterials = new ArrayCollection();
        if (is_null($learningMaterials)) {
            return;
        }

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    /**
     * @inheritdoc
     */
    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial)
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial)
    {
        if ($this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->removeElement($learningMaterial);
        }
    }

    /**
     * @inheritdoc
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }

    /**
     * @inheritdoc
     */
    public function setAncestor(CourseInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    /**
     * @inheritdoc
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * @inheritdoc
     */
    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    /**
     * @inheritdoc
     */
    public function addDescendant(CourseInterface $descendant)
    {
        $this->descendants->add($descendant);
    }

    /**
     * @inheritdoc
     */
    public function removeDescendant(CourseInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }

    /**
     * @inheritdoc
     */
    public function getDescendants()
    {
        return $this->descendants;
    }

    /**
     * @inheritdoc
     */
    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedCourse($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedCourse($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addCohort(CohortInterface $cohort)
    {
        if (!$this->cohorts->contains($cohort)) {
            $this->cohorts->add($cohort);
            $cohort->addCourse($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCohort(CohortInterface $cohort)
    {
        if ($this->cohorts->contains($cohort)) {
            $this->cohorts->removeElement($cohort);
            $cohort->removeCourse($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addTerm(TermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addCourse($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeTerm(TermInterface $term)
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeCourse($this);
        }
    }
}
