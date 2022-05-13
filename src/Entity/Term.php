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
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\CoursesEntity;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SessionsEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Repository\TermRepository;

#[ORM\Table(name: 'term')]
#[ORM\UniqueConstraint(name: 'unique_term_title', columns: ['vocabulary_id', 'title', 'parent_term_id'])]
#[ORM\Entity(repositoryClass: TermRepository::class)]
#[IA\Entity]
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

    #[ORM\Column(name: 'term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $courses;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\ManyToOne(targetEntity: 'Term', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_term_id', referencedColumnName: 'term_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?TermInterface $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'Term')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $children;

    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $programYears;

    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'terms')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $sessions;

    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $sessionObjectives;

    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $courseObjectives;

    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'terms')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $programYearObjectives;

    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: 'Vocabulary', inversedBy: 'terms')]
    #[ORM\JoinColumn(name: 'vocabulary_id', referencedColumnName: 'vocabulary_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected VocabularyInterface $vocabulary;

    #[ORM\ManyToMany(targetEntity: 'AamcResourceType', inversedBy: 'terms')]
    #[ORM\JoinTable(name: 'term_x_aamc_resource_type')]
    #[ORM\JoinColumn(name: 'term_id', referencedColumnName: 'term_id')]
    #[ORM\InverseJoinColumn(name: 'resource_type_id', referencedColumnName: 'resource_type_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $aamcResourceTypes;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

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

    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addTerm($this);
        }
    }

    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeTerm($this);
        }
    }

    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addTerm($this);
        }
    }

    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeTerm($this);
        }
    }

    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addTerm($this);
        }
    }

    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeTerm($this);
        }
    }

    public function getVocabulary(): VocabularyInterface
    {
        return $this->vocabulary;
    }

    public function setVocabulary(VocabularyInterface $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    public function getParent(): ?TermInterface
    {
        return $this->parent;
    }

    public function setParent(TermInterface $parent = null)
    {
        $this->parent = $parent;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(TermInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    public function removeChild(TermInterface $child)
    {
        $this->children->removeElement($child);
    }

    public function hasChildren(): bool
    {
        return (!$this->children->isEmpty()) ? true : false;
    }

    public function setAamcResourceTypes(Collection $aamcResourceTypes)
    {
        $this->aamcResourceTypes = new ArrayCollection();

        foreach ($aamcResourceTypes as $aamcResourceType) {
            $this->addAamcResourceType($aamcResourceType);
        }
    }

    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        if (!$this->aamcResourceTypes->contains($aamcResourceType)) {
            $this->aamcResourceTypes->add($aamcResourceType);
        }
    }

    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        $this->aamcResourceTypes->removeElement($aamcResourceType);
    }

    public function getAamcResourceTypes(): Collection
    {
        return $this->aamcResourceTypes;
    }

    public function getIndexableCourses(): array
    {
        $sessionCourses = $this->sessions->map(fn(SessionInterface $session) => $session->getCourse());

        return array_merge(
            $this->courses->toArray(),
            $sessionCourses->toArray()
        );
    }
}
