<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation as IS;
use App\Traits\IdentifiableEntity;
use App\Traits\ObjectiveRelationshipEntity;
use App\Traits\StringableIdEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProgramYearObjective
 *
 * @ORM\Table(name="program_year_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_7A16FDD673484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_7A16FDD6CB2B0673", columns={"program_year_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="program_year_objective_uniq", columns={"program_year_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass="App\Entity\Repository\ProgramYearObjectiveRepository")
 * @IS\Entity
 */
class ProgramYearObjective implements ProgramYearObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use ObjectiveRelationshipEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="program_year_objective_id", type="integer")
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
     * @var ProgramYearInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="ProgramYear", inversedBy="programYearObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $programYear;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $position;

    /**
     * @var ObjectiveInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="programYearObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $objective;

    /**
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="programYearObjectives")
     * @ORM\JoinTable(name="program_year_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(
     *       name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $terms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear): void
    {
        $this->programYear = $programYear;
    }

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear(): ProgramYearInterface
    {
        return $this->programYear;
    }
}
