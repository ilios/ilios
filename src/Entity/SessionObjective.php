<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation as IS;
use App\Traits\IdentifiableEntity;
use App\Traits\ObjectiveRelationshipEntity;
use App\Traits\SessionConsolidationEntity;
use App\Traits\StringableIdEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SessionObjective
 *
 * @ORM\Table(name="session_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_FA74B40B73484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_FA74B40B613FECDF", columns={"session_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="session_objective_uniq", columns={"session_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass="App\Entity\Repository\SessionObjectiveRepository")
 * @IS\Entity
 */
class SessionObjective implements SessionObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;
    use ObjectiveRelationshipEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="session_objective_id", type="integer")
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
     * @var SessionInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="objectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $session;

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
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="sessionObjectives")
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
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="sessionObjectives")
     * @ORM\JoinTable(name="session_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_objective_id", referencedColumnName="session_objective_id", onDelete="CASCADE")
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
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->session->getCourse()];
    }
}
