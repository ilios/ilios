<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\DirectorsEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\LockableEntity;
use App\Repository\ProgramYearRepository;
use Override;

#[ORM\Table(name: 'program_year')]
#[ORM\Entity(repositoryClass: ProgramYearRepository::class)]
#[IA\Entity]
class ProgramYear implements ProgramYearInterface
{
    use LockableEntity;
    use ProgramYearObjectivesEntity;
    use StringableIdEntity;
    use DirectorsEntity;

    #[ORM\Column(name: 'program_year_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'start_year', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $startYear;

    #[ORM\Column(name: 'locked', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $locked;

    #[ORM\Column(name: 'archived', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $archived;

    #[ORM\ManyToOne(targetEntity: Program::class, inversedBy: 'programYears')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected ProgramInterface $program;

    #[ORM\OneToOne(mappedBy: 'programYear', targetEntity: Cohort::class)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CohortInterface $cohort;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_director')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directors;

    #[ORM\ManyToMany(targetEntity: Competency::class, inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_competency')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'competency_id', referencedColumnName: 'competency_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    #[ORM\ManyToMany(targetEntity: Term::class, inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_term')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\OneToMany(mappedBy: 'programYear', targetEntity: ProgramYearObjective::class)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programYearObjectives;

    public function __construct()
    {
        $this->archived = false;
        $this->locked = false;
        $this->directors = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
    }

    #[Override]
    public function setStartYear(int $startYear): void
    {
        $this->startYear = $startYear;
    }

    #[Override]
    public function getStartYear(): int
    {
        return $this->startYear;
    }

    #[Override]
    public function setProgram(ProgramInterface $program): void
    {
        $this->program = $program;
    }

    #[Override]
    public function getProgram(): ProgramInterface
    {
        return $this->program;
    }

    #[Override]
    public function setCohort(CohortInterface $cohort): void
    {
        $this->cohort = $cohort;
    }

    #[Override]
    public function getCohort(): ?CohortInterface
    {
        return $this->cohort;
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->program->getSchool();
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function isArchived(): bool
    {
        return $this->archived;
    }

    #[Override]
    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
    #[Override]
    public function setTerms(?Collection $terms = null): void
    {
        $this->terms = new ArrayCollection();
        if (is_null($terms)) {
            return;
        }

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    #[Override]
    public function addTerm(TermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }

    #[Override]
    public function removeTerm(TermInterface $term): void
    {
        $this->terms->removeElement($term);
    }

    #[Override]
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    #[Override]
    public function setCompetencies(Collection $competencies): void
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    #[Override]
    public function addCompetency(CompetencyInterface $competency): void
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
        }
    }

    #[Override]
    public function removeCompetency(CompetencyInterface $competency): void
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
        }
    }

    #[Override]
    public function getCompetencies(): Collection
    {
        return $this->competencies;
    }
}
