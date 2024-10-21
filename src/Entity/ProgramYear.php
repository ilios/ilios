<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CategorizableEntity;
use App\Traits\CompetenciesEntity;
use App\Traits\DirectorsEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ArchivableEntity;
use App\Traits\LockableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\ProgramYearRepository;

#[ORM\Table(name: 'program_year')]
#[ORM\Entity(repositoryClass: ProgramYearRepository::class)]
#[IA\Entity]
class ProgramYear implements ProgramYearInterface
{
    use IdentifiableEntity;
    use LockableEntity;
    use ArchivableEntity;
    use ProgramYearObjectivesEntity;
    use CategorizableEntity;
    use StringableIdEntity;
    use DirectorsEntity;
    use CompetenciesEntity;

    #[ORM\Column(name: 'program_year_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

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

    #[ORM\ManyToOne(targetEntity: 'Program', inversedBy: 'programYears')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected ProgramInterface $program;

    #[ORM\OneToOne(mappedBy: 'programYear', targetEntity: 'Cohort')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected CohortInterface $cohort;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_director')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directors;

    #[ORM\ManyToMany(targetEntity: 'Competency', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_competency')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'competency_id', referencedColumnName: 'competency_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_term')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\OneToMany(mappedBy: 'programYear', targetEntity: 'ProgramYearObjective')]
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

    public function setStartYear(int $startYear): void
    {
        $this->startYear = $startYear;
    }

    public function getStartYear(): int
    {
        return $this->startYear;
    }

    public function setProgram(ProgramInterface $program): void
    {
        $this->program = $program;
    }

    public function getProgram(): ProgramInterface
    {
        return $this->program;
    }

    public function setCohort(CohortInterface $cohort): void
    {
        $this->cohort = $cohort;
    }

    public function getCohort(): ?CohortInterface
    {
        return $this->cohort;
    }

    public function getSchool(): SchoolInterface
    {
        return $this->program->getSchool();
    }
}
