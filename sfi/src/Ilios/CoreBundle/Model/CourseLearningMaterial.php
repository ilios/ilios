<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CourseLearningMaterial
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="course_learning_material")
 */
class CourseLearningMaterial implements CourseLearningMaterialInterface
{
//    use IdentifiableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=14, name="course_learning_material_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $courseLearningMaterialId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $required;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $publicNotes;

    /**
     * @var CourseInterface
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="courseLearningMaterials")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     */
    protected $course;

    /**
     * @var LearningMaterialInterface
     *
     * @ORM\ManyToOne(targetEntity="LearningMaterial", inversedBy="courseLearningMaterials")
     * @ORM\JoinColumn(name="learning_material_id", referencedColumnName="learning_material_id")
     */
    protected $learningMaterial;

    /**
     * @var ArrayCollection|MeshDescriptor[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courseLearningMaterials")
     * @ORM\JoinTable(
     *      name="course_learning_material_x_mesh",
     *      joinColumns={@ORM\JoinColumn(name="course_learning_material_id", referencedColumnName="")}
     * )
     */
    protected $meshDescriptors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->courseLearningMaterialId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->courseLearningMaterialId : $this->id;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $publicNotes
     */
    public function setPublicNotes($publicNotes)
    {
        $this->publicNotes = $publicNotes;
    }

    /**
     * @return boolean
     */
    public function hasPublicNotes()
    {
        return $this->publicNotes;
    }

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterial = $learningMaterial;
    }

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial()
    {
        return $this->learningMaterial;
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
     * @param MeshDescriptorInterface $meshDescriptors
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptors)
    {
        $this->meshDescriptors->add($meshDescriptors);
    }

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors;
    }
}
