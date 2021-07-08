<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntity;
use App\Traits\StudentAdvisorsEntity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\AdministratorsEntity;
use App\Traits\CategorizableEntity;
use App\Traits\CohortsEntity;
use App\Traits\DirectorsEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\PublishableEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ArchivableEntity;
use App\Traits\LockableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\SessionsEntity;
use App\Traits\SchoolEntity;
use App\Repository\CourseRepository;

/**
 * Class Course
 * @IS\Entity
 */
#[ORM\Table(name: 'course')]
#[ORM\Index(name: 'title_course_k', columns: ['course_id', 'title'])]
#[ORM\Index(name: 'external_id', columns: ['external_id'])]
#[ORM\Index(name: 'clerkship_type_id', columns: ['clerkship_type_id'])]
#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course implements CourseInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LockableEntity;
    use ArchivableEntity;
    use SessionsEntity;
    use SchoolEntity;
    use PublishableEntity;
    use CategorizableEntity;
    use CohortsEntity;
    use MeshDescriptorsEntity;
    use DirectorsEntity;
    use AdministratorsEntity;
    use StudentAdvisorsEntity;
    use CourseObjectivesEntity;
    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'course_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    protected $title;
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 10
     * )
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(type: 'smallint', name: 'course_level')]
    protected $level;
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'year', type: 'smallint')]
    protected $year;
    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(type: 'date', name: 'start_date')]
    protected $startDate;
    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(type: 'date', name: 'end_date')]
    protected $endDate;
    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 255, name: 'external_id', nullable: true)]
    protected $externalId;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $locked;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $archived;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean', name: 'published_as_tbd')]
    protected $publishedAsTbd;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $published;
    /**
     * @var CourseClerkshipTypeInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'CourseClerkshipType', inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'clerkship_type_id', referencedColumnName: 'course_clerkship_type_id')]
    protected $clerkshipType;
    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    protected $school;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedCourses')]
    #[ORM\JoinTable(name: 'course_director')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directors;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredCourses')]
    #[ORM\JoinTable(name: 'course_administrator')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administrators;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'studentAdvisedCourses')]
    #[ORM\JoinTable(name: 'course_student_advisor')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $studentAdvisors;
    /**
     * @var ArrayCollection|CohortInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Cohort', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_cohort')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $cohorts;

    /**
     * @var ArrayCollection|TermInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_term')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $terms;

    /**
     * @var ArrayCollection|CourseObjectiveInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CourseObjective', mappedBy: 'course')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $courseObjectives;
    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *    joinColumns={
     *    },
     *    inverseJoinColumns={
     *    }
     * )
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_mesh')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $meshDescriptors;
    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CourseLearningMaterial', mappedBy: 'course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;
    /**
     * @var ArrayCollection|SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Session', mappedBy: 'course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessions;
    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sequenceBlocks;
    /**
     * @var CourseInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'course_id')]
    protected $ancestor;
    /**
     * @var CourseInterface
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Course', mappedBy: 'ancestor')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $descendants;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->directors = new ArrayCollection();
        $this->administrators = new ArrayCollection();
        $this->studentAdvisors = new ArrayCollection();
        $this->cohorts = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->learningMaterials = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->sequenceBlocks = new ArrayCollection();
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
    public function setStartDate(DateTime $startDate = null)
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
    public function setEndDate(DateTime $endDate = null)
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
    public function getAncestorOrSelf()
    {
        $ancestor = $this->getAncestor();

        return $ancestor ? $ancestor : $this;
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
    /**
     * @inheritdoc
     */
    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredCourse($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeAdministrator(UserInterface $administrator)
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredCourse($this);
        }
    }
    public function addStudentAdvisor(UserInterface $studentAdvisor)
    {
        if (!$this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->add($studentAdvisor);
            $studentAdvisor->addStudentAdvisedCourse($this);
        }
    }
    public function removeStudentAdvisor(UserInterface $studentAdvisor)
    {
        if ($this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->removeElement($studentAdvisor);
            $studentAdvisor->removeStudentAdvisedCourse($this);
        }
    }
    /**
     * When and objective is removed from a course it needs to remove any relationships
     * to children that belong to sessions in that course
     * @param CourseObjectiveInterface $courseObjective
     */
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if ($this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->removeElement($courseObjective);
            /* @var SessionInterface $session */
            foreach ($this->getSessions() as $session) {
                /* @var SessionObjectiveInterface $sessionObjective */
                foreach ($session->getSessionObjectives() as $sessionObjective) {
                    $sessionObjective->removeCourseObjective($courseObjective);
                }
            }
        }
    }
    /**
     * @param Collection $sequenceBlocks
     */
    public function setSequenceBlocks(Collection $sequenceBlocks)
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }
    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }
    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        $this->sequenceBlocks->removeElement($sequenceBlock);
    }
    /**
     * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
     */
    public function getSequenceBlocks()
    {
        return $this->sequenceBlocks;
    }
    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this];
    }
}
