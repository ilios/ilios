<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntity;
use App\Traits\StudentAdvisorsEntity;
use App\Traits\TitledNullableEntity;
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
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ArchivableEntity;
use App\Traits\LockableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\SessionsEntity;
use App\Traits\SchoolEntity;
use App\Repository\CourseRepository;

#[ORM\Table(name: 'course')]
#[ORM\Index(columns: ['course_id', 'title'], name: 'title_course_k')]
#[ORM\Index(columns: ['external_id'], name: 'external_id')]
#[ORM\Index(columns: ['clerkship_type_id'], name: 'clerkship_type_id')]
#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[IA\Entity]
class Course implements CourseInterface
{
    use IdentifiableEntity;
    use TitledNullableEntity;
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

    #[ORM\Column(name: 'course_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected ?string $title = null;

    #[ORM\Column(name: 'course_level', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 1, max: 10)]
    protected int $level;

    #[ORM\Column(name: 'year', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $year;

    #[ORM\Column(name: 'start_date', type: 'date')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $startDate;

    #[ORM\Column(name: 'end_date', type: 'date')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $endDate;

    #[ORM\Column(name: 'external_id', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    protected ?string $externalId = null;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $locked;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $archived;

    #[ORM\Column(name: 'published_as_tbd', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $publishedAsTbd;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $published;

    #[ORM\ManyToOne(targetEntity: 'CourseClerkshipType', inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'clerkship_type_id', referencedColumnName: 'course_clerkship_type_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CourseClerkshipTypeInterface $clerkshipType = null;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedCourses')]
    #[ORM\JoinTable(name: 'course_director')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directors;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredCourses')]
    #[ORM\JoinTable(name: 'course_administrator')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'studentAdvisedCourses')]
    #[ORM\JoinTable(name: 'course_student_advisor')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $studentAdvisors;

    #[ORM\ManyToMany(targetEntity: 'Cohort', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_cohort')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $cohorts;

    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_term')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'CourseObjective')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseObjectives;

    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_x_mesh')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'mesh_descriptor_uid',
        referencedColumnName: 'mesh_descriptor_uid',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $meshDescriptors;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'CourseLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learningMaterials;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'Session')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessions;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'CurriculumInventorySequenceBlock')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sequenceBlocks;

    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'course_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CourseInterface $ancestor = null;

    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: 'Course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descendants;

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

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setStartDate(?DateTime $startDate = null): void
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(?DateTime $endDate = null): void
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setClerkshipType(?CourseClerkshipTypeInterface $clerkshipType = null): void
    {
        $this->clerkshipType = $clerkshipType;
    }

    public function getClerkshipType(): ?CourseClerkshipTypeInterface
    {
        return $this->clerkshipType;
    }

    public function setLearningMaterials(?Collection $learningMaterials = null): void
    {
        $this->learningMaterials = new ArrayCollection();
        if (is_null($learningMaterials)) {
            return;
        }

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    public function addLearningMaterial(CourseLearningMaterialInterface $learningMaterial): void
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    public function removeLearningMaterial(CourseLearningMaterialInterface $learningMaterial): void
    {
        if ($this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->removeElement($learningMaterial);
        }
    }

    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }

    public function setAncestor(?CourseInterface $ancestor = null): void
    {
        $this->ancestor = $ancestor;
    }

    public function getAncestor(): ?CourseInterface
    {
        return $this->ancestor;
    }

    public function getAncestorOrSelf(): CourseInterface
    {
        $ancestor = $this->getAncestor();

        return $ancestor ?: $this;
    }

    public function setDescendants(Collection $descendants): void
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    public function addDescendant(CourseInterface $descendant): void
    {
        $this->descendants->add($descendant);
    }

    public function removeDescendant(CourseInterface $descendant): void
    {
        $this->descendants->removeElement($descendant);
    }

    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    public function addDirector(UserInterface $director): void
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedCourse($this);
        }
    }

    public function removeDirector(UserInterface $director): void
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedCourse($this);
        }
    }

    public function addCohort(CohortInterface $cohort): void
    {
        if (!$this->cohorts->contains($cohort)) {
            $this->cohorts->add($cohort);
            $cohort->addCourse($this);
        }
    }

    public function removeCohort(CohortInterface $cohort): void
    {
        if ($this->cohorts->contains($cohort)) {
            $this->cohorts->removeElement($cohort);
            $cohort->removeCourse($this);
        }
    }

    public function addTerm(TermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addCourse($this);
        }
    }

    public function removeTerm(TermInterface $term): void
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeCourse($this);
        }
    }

    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredCourse($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredCourse($this);
        }
    }
    public function addStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if (!$this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->add($studentAdvisor);
            $studentAdvisor->addStudentAdvisedCourse($this);
        }
    }
    public function removeStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if ($this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->removeElement($studentAdvisor);
            $studentAdvisor->removeStudentAdvisedCourse($this);
        }
    }

    /**
     * When and objective is removed from a course it needs to remove any relationships
     * to children that belong to sessions in that course
     */
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if ($this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->removeElement($courseObjective);
            /** @var SessionInterface $session */
            foreach ($this->getSessions() as $session) {
                /** @var SessionObjectiveInterface $sessionObjective */
                foreach ($session->getSessionObjectives() as $sessionObjective) {
                    $sessionObjective->removeCourseObjective($courseObjective);
                }
            }
        }
    }

    public function setSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    public function addSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        $this->sequenceBlocks->removeElement($sequenceBlock);
    }

    public function getSequenceBlocks(): Collection
    {
        return $this->sequenceBlocks;
    }

    public function getIndexableCourses(): array
    {
        return [$this];
    }
}
