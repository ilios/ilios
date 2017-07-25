<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\CategorizableEntity;
use Ilios\CoreBundle\Traits\CompetenciesEntity;
use Ilios\CoreBundle\Traits\DirectorsEntity;
use Ilios\CoreBundle\Traits\ObjectivesEntity;
use Ilios\CoreBundle\Traits\PublishableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\ArchivableEntity;
use Ilios\CoreBundle\Traits\LockableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StewardedEntity;

/**
 * Class ProgramYear
 *
 * @ORM\Table(name="program_year")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\ProgramYearRepository")
 *
 * @IS\Entity
 */
class ProgramYear implements ProgramYearInterface
{
    use IdentifiableEntity;
    use LockableEntity;
    use ArchivableEntity;
    use StewardedEntity;
    use ObjectivesEntity;
    use PublishableEntity;
    use CategorizableEntity;
    use StringableIdEntity;
    use DirectorsEntity;
    use CompetenciesEntity;

    /**
    * @var int
    *
    * @ORM\Column(name="program_year_id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    *
    * @Assert\Type(type="integer")
    *
    * @IS\Expose
    * @IS\Type("integer")
     * @IS\ReadOnly
    */
    protected $id;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="start_year", type="smallint")
     */
    protected $startYear;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    protected $archived;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @ORM\Column(name="published_as_tbd", type="boolean")
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
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $published;

    /**
     * @var ProgramInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="programYears")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $program;

    /**
    * @var CohortInterface
    *
    * @ORM\OneToOne(targetEntity="Cohort", mappedBy="programYear")
    *
    * @IS\Expose
    * @IS\Type("entity")
    */
    protected $cohort;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="programYears")
     * @ORM\JoinTable(name="program_year_director",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Competency", inversedBy="programYears")
     * @ORM\JoinTable(name="program_year_x_competency",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="competency_id", referencedColumnName="competency_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $competencies;

    /**
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="programYears")
     * @ORM\JoinTable(name="program_year_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $terms;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="programYears")
     * @ORM\JoinTable(name="program_year_x_objective",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $objectives;

    /**
     * @var ArrayCollection|ProgramYearStewardInterface[]
     *
     * @ORM\OneToMany(targetEntity="ProgramYearSteward", mappedBy="programYear")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $stewards;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->archived = false;
        $this->locked = false;
        $this->publishedAsTbd = false;
        $this->published = false;
        $this->directors = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->stewards = new ArrayCollection();
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
