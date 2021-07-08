<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CategorizableEntity;
use App\Traits\CompetenciesEntity;
use App\Traits\DirectorsEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ArchivableEntity;
use App\Traits\LockableEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\ProgramYearRepository;

/**
 * Class ProgramYear
 * @IS\Entity
 */
#[ORM\Table(name: 'program_year')]
#[ORM\Entity(repositoryClass: ProgramYearRepository::class)]
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
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'program_year_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'start_year', type: 'smallint')]
    protected $startYear;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'locked', type: 'boolean')]
    protected $locked;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'archived', type: 'boolean')]
    protected $archived;

    /**
     * @var ProgramInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Program', inversedBy: 'programYears')]
    #[ORM\JoinColumn(name: 'program_id', referencedColumnName: 'program_id')]
    protected $program;

    /**
     * @var CohortInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'Cohort', mappedBy: 'programYear')]
    protected $cohort;

    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_director')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Competency', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_competency')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'competency_id', referencedColumnName: 'competency_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $competencies;

    /**
     * @var ArrayCollection|TermInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'programYears')]
    #[ORM\JoinTable(name: 'program_year_x_term')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $terms;

    /**
     * @var ArrayCollection|ProgramYearObjective[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'programYear')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
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
    public function getStartYear()
    {
        return $this->startYear;
    }

    /**
     * @param ProgramInterface $program
     */
    public function setProgram(ProgramInterface $program)
    {
        $this->program = $program;
    }

    /**
     * @return ProgramInterface
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @inheritdoc
     */
    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @inheritdoc
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($program = $this->getProgram()) {
            return $program->getSchool();
        }
        return null;
    }
}
