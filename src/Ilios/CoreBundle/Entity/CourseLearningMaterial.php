<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class CourseLearningMaterial
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="course_learning_material")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class CourseLearningMaterial implements CourseLearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_learning_material_id", type="integer")
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
     * @ORM\Column(name="notes", type="text", nullable=true)
     *
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $required;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notes_are_public", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publicNotes")
     */
    protected $publicNotes;

    /**
     * @var CourseInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="learningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $course;

    /**
     * @var LearningMaterialInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="LearningMaterial", inversedBy="courseLearningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="learning_material_id", referencedColumnName="learning_material_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("learningMaterial")
     */
    protected $learningMaterial;

    /**
     * @var ArrayCollection|MeshDescriptor[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courseLearningMaterials")
     * @ORM\JoinTable(name="course_learning_material_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_learning_material_id", referencedColumnName="course_learning_material_id")
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
        $this->meshDescriptors = new ArrayCollection();
        $this->publicNotes = false;
        $this->required = false;
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
     * @inheritdoc
     */
    public function getCourse()
    {
        if ($this->course && ! $this->course->isDeleted()) {
            return $this->course;
        }
        return null;
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
}
