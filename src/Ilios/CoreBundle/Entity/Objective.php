<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Objective
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="objective")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Objective implements ObjectiveInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="objective_id", type="integer")
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
     * @JMS\Expose
     * @JMS\Type("string")
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
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $competency;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="objectives")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="objectives")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="objectives")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $parents;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", mappedBy="parents")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     */
    protected $meshDescriptors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
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
     * @param Collection $courses
     */
    public function setCourses(Collection $courses)
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course)
    {
        $this->courses->add($course);
    }

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @param Collection $programYears
     */
    public function setProgramYears(Collection $programYears)
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->add($programYear);
    }

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears()
    {
        return $this->programYears;
    }

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions;
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
        $this->parents->add($parent);
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
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->add($meshDescriptor);
    }

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
