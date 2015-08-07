<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ArchivableEntity;
use Ilios\CoreBundle\Traits\LockableEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class ProgramYear
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="program_year")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class ProgramYear implements ProgramYearInterface
{
    use IdentifiableEntity;
    use LockableEntity;
    use ArchivableEntity;

    /**
    * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
    * @var int
    *
    * @ORM\Column(name="program_year_id", type="integer")
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
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("startYear")
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
     * @JMS\Expose
     * @JMS\Type("boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    protected $deleted;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
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
     * @JMS\Expose
     * @JMS\Type("boolean")
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
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @ORM\Column(name="published_as_tbd", type="boolean")
     */
    protected $publishedAsTbd;

    /**
     * @var ProgramInterface
     *
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="programYears")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $program;

    /**
    * @var CohortInterface
    *
    * @ORM\OneToOne(targetEntity="Cohort", mappedBy="programYear")
    *
    * @JMS\Expose
    * @JMS\Type("string")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $competencies;

    /**
     * @var ArrayCollection|DisciplineInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Discipline", inversedBy="programYears")
     * @ORM\JoinTable(name="program_year_x_discipline",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="discipline_id", referencedColumnName="discipline_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $disciplines;

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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $objectives;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent", inversedBy="programYears")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("publishEvent")
     */
    protected $publishEvent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deleted = false;
        $this->archived = false;
        $this->locked = false;
        $this->publishedAsTbd = false;
        $this->directors = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->disciplines = new ArrayCollection();
        $this->objectives = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->programYearId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->programYearId : $this->id;
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    /**
     * @return boolean
     */
    public function isPublishedAsTbd()
    {
        return $this->publishedAsTbd;
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
     * @param Collection $directors
     */
    public function setDirectors(Collection $directors)
    {
        $this->directors = new ArrayCollection();

        foreach ($directors as $director) {
            $this->addDirector($director);
        }
    }

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director)
    {
        $this->directors->add($director);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies)
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }
    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency)
    {
        $this->competencies->add($competency);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies()
    {
        return $this->competencies;
    }

    /**
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines)
    {
        $this->disciplines = new ArrayCollection();

        foreach ($disciplines as $discipline) {
            $this->addDiscipline($discipline);
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline)
    {
        $this->disciplines->add($discipline);
    }

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines()
    {
        return $this->disciplines;
    }

    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives)
    {
        $this->objectives = new ArrayCollection();

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        $this->objectives->add($objective);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;
    }

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }

    /**
     * @param CohortInterface
     */
    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return CohortInterface
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
