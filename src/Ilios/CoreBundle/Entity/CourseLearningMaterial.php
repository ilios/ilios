<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\LearningMaterialRelationshipEntity;
use Ilios\CoreBundle\Traits\MeshDescriptorsEntity;
use Ilios\CoreBundle\Traits\SortableEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class CourseLearningMaterial
 *
 * @ORM\Table(name="course_learning_material")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CourseLearningMaterialRepository")
 *
 * @IS\Entity
 */
class CourseLearningMaterial implements CourseLearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use MeshDescriptorsEntity;
    use SortableEntity;
    use LearningMaterialRelationshipEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_learning_material_id", type="integer")
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
     * @ORM\Column(name="notes", type="text", nullable=true)
     *
     *
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
    protected $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
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
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $publicNotes;

    /**
     * @var CourseInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="learningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
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
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $learningMaterial;

    /**
     * @var ArrayCollection|MeshDescriptor[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courseLearningMaterials")
     * @ORM\JoinTable(name="course_learning_material_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(
     *       name="course_learning_material_id",
     *       referencedColumnName="course_learning_material_id",
     *       onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $meshDescriptors;

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
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $endDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
        $this->publicNotes = false;
        $this->required = false;
        $this->position = 0;
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
        return $this->course;
    }
}
