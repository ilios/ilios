<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\MeshDescriptorsEntity;
use Ilios\CoreBundle\Traits\SortableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;

/**
 * Class Objective
 *
 * @ORM\Table(name="objective")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\ObjectiveRepository")
 *
 * @IS\Entity
 */
class Objective implements ObjectiveInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use CoursesEntity;
    use SessionsEntity;
    use ProgramYearsEntity;
    use StringableIdEntity;
    use MeshDescriptorsEntity;
    use SortableEntity;


    /**
     * @var int
     *
     * @ORM\Column(name="objective_id", type="integer")
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
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    protected $title;

    /**
     * @var CompetencyInterface
     *
     * @ORM\ManyToOne(targetEntity="Competency", inversedBy="objectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="competency_id", referencedColumnName="competency_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $competency;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="objectives")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="objectives")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="objectives")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessions;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="children")
     * @ORM\JoinTable("objective_x_objective",
     *   joinColumns={@ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="parent_objective_id", referencedColumnName="objective_id")}
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $parents;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", mappedBy="parents")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $children;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="objectives")
     * @ORM\JoinTable(name="objective_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $meshDescriptors;

    /**
     * @var ObjectiveInterface
     *
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="objective_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $ancestor;

    /**
     * @var ObjectiveInterface
     *
     * @ORM\OneToMany(targetEntity="Objective", mappedBy="ancestor")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $descendants;

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
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->courses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function setCompetency(CompetencyInterface $competency = null)
    {
        $this->competency = $competency;
    }

    /**
     * @return CompetencyInterface
     */
    public function getCompetency()
    {
        return $this->competency;
    }

    /**
     * @param Collection $parents
     */
    public function setParents(Collection $parents)
    {
        $this->parents = new ArrayCollection();

        foreach ($parents as $parent) {
            $this->addParent($parent);
        }
    }

    /**
     * @param ObjectiveInterface $parent
     */
    public function addParent(ObjectiveInterface $parent)
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
        }
    }

    /**
     * @param ObjectiveInterface $parent
     */
    public function removeParent(ObjectiveInterface $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param ObjectiveInterface $child
     */
    public function addChild(ObjectiveInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @param ObjectiveInterface $child
     */
    public function removeChild(ObjectiveInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addObjective($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeObjective($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addObjective($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeObjective($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addObjective($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeObjective($this);
        }
    }

    /**
     * @param ObjectiveInterface $parent
     */
    public function setAncestor(ObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    /**
     * @return ObjectiveInterface
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * If the objective has no ancestor then we need to objective itself
     *
     * @return ObjectiveInterface
     */
    public function getAncestorOrSelf()
    {
        $ancestor = $this->getAncestor();

        return $ancestor?$ancestor:$this;
    }

    /**
     * @param Collection $descendants
     */
    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    /**
     * @param ObjectiveInterface $descendant
     */
    public function addDescendant(ObjectiveInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    /**
     * @param ObjectiveInterface $descendant
     */
    public function removeDescendant(ObjectiveInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getDescendants()
    {
        return $this->descendants;
    }
}
