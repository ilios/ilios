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
 * Class CourseObjective
 *
 * @ORM\Table(name="course_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_3B37B1AD73484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_3B37B1AD591CC992", columns={"course_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="course_objective_uniq", columns={"course_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass="App\Entity\Repository\CourseObjectiveRepository")
 * @IS\Entity
 */
class CourseObjective implements CourseObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use ObjectiveRelationshipEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_objective_id", type="integer")
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
     * @var CourseInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="courseObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $course;

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
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="courseObjectives")
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
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="courseObjectives")
     * @ORM\JoinTable(name="course_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_objective_id", referencedColumnName="course_objective_id", onDelete="CASCADE")
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
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course): void
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
     */
    public function getCourse(): CourseInterface
    {
        return $this->course;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->getCourse()];
    }
}
