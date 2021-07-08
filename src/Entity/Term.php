<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntity;
use App\Traits\ProgramYearObjectivesEntity;
use App\Traits\SessionObjectivesEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\CoursesEntity;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SessionsEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Repository\TermRepository;

/**
 * Class Term
 * @IS\Entity
 */
#[ORM\Table(name: 'term')]
#[ORM\UniqueConstraint(name: 'unique_term_title', columns: ['vocabulary_id', 'title', 'parent_term_id'])]
#[ORM\Entity(repositoryClass: TermRepository::class)]
class Term implements TermInterface
{
    use CoursesEntity;
    use DescribableEntity;
    use IdentifiableEntity;
    use ProgramYearsEntity;
    use SessionsEntity;
    use StringableIdEntity;
    use TitledEntity;
    use ActivatableEntity;
    use SessionObjectivesEntity;
    use CourseObjectivesEntity;
    use ProgramYearObjectivesEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courses;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var TermInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Term', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_term_id', referencedColumnName: 'term_id')]
    protected $parent;

    /**
     * @var ArrayCollection|TermInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'Term')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $children;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessions;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $sessionObjectives;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $courseObjectives;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $programYearObjectives;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    protected $title;

    /**
     * @var VocabularyInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Vocabulary', inversedBy: 'terms')]
    #[ORM\JoinColumn(name: 'vocabulary_id', referencedColumnName: 'vocabulary_id', nullable: false)]
    protected $vocabulary;

    /**
     * @var ArrayCollection|AamcResourceTypeInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'AamcResourceType', inversedBy: 'terms')]
    #[ORM\JoinTable(name: 'term_x_aamc_resource_type')]
    #[ORM\JoinColumn(name: 'term_id', referencedColumnName: 'term_id')]
    #[ORM\InverseJoinColumn(name: 'resource_type_id', referencedColumnName: 'resource_type_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $aamcResourceTypes;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $active;

    public function __construct()
    {
        $this->aamcResourceTypes = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * @inheritdoc
     */
    public function setVocabulary(VocabularyInterface $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function setParent(TermInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @inheritdoc
     */
    public function addChild(TermInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeChild(TermInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return (!$this->children->isEmpty()) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function setAamcResourceTypes(Collection $aamcResourceTypes)
    {
        $this->aamcResourceTypes = new ArrayCollection();

        foreach ($aamcResourceTypes as $aamcResourceType) {
            $this->addAamcResourceType($aamcResourceType);
        }
    }

    /**
     * @inheritdoc
     */
    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        if (!$this->aamcResourceTypes->contains($aamcResourceType)) {
            $this->aamcResourceTypes->add($aamcResourceType);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        $this->aamcResourceTypes->removeElement($aamcResourceType);
    }

    /**
     * @inheritdoc
     */
    public function getAamcResourceTypes()
    {
        return $this->aamcResourceTypes;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        $sessionCourses = $this->sessions->map(function (SessionInterface $session) {
            return $session->getCourse();
        });

        return array_merge(
            $this->courses->toArray(),
            $sessionCourses->toArray()
        );
    }
}
