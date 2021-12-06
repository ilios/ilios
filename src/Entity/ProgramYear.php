<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CategorizableEntity;
use App\Traits\CompetenciesEntity;
use App\Traits\DirectorsEntity;
use App\Traits\StringableIdEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ArchivableEntity;
use App\Traits\LockableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\ProgramYearRepository;

/**
 * Class ProgramYear
 */
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

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'program_year_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'start_year', type: 'smallint')]
    #[IA\Expose]
    #[IA\Type('integer')]
    protected $startYear;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'locked', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $locked;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'archived', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $archived;

    /**
     * @var ProgramInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Program', inversedBy: 'programYears')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $program;

    /**
     * @var CohortInterface
     */
    #[ORM\OneToOne(targetEntity: 'Cohort', mappedBy: 'programYear')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $cohort;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_director')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Competency', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_competency')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'competency_id', referencedColumnName: 'competency_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $competencies;

    /**
     * @var ArrayCollection|TermInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_term')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    /**
     * @var ArrayCollection|ProgramYearObjective[]
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'programYear')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programYearObjectives;

    public function __construct()
    {
        $this->archived = false;
        $this->locked = false;
        $this->directors = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
    }

    /**
     * @param int $startYear
     */
    public function setStartYear($startYear)
    {
        $this->startYear = $startYear;
    }

    /**
     * @return int
     */
    public function getStartYear(): int
    {
        return $this->startYear;
    }

    public function setProgram(ProgramInterface $program)
    {
        $this->program = $program;
    }

    /**
     * @return ProgramInterface
     */
    public function getProgram(): ProgramInterface
    {
        return $this->program;
    }

    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    public function getCohort(): CohortInterface
    {
        return $this->cohort;
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($program = $this->getProgram()) {
            return $program->getSchool();
        }
        return null;
    }
}
