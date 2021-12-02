<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearningMaterialRelationshipEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SortableEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\CourseLearningMaterialRepository;

/**
 * Class CourseLearningMaterial
 */
#[ORM\Table(name: 'course_learning_material')]
#[ORM\Entity(repositoryClass: CourseLearningMaterialRepository::class)]
#[IA\Entity]
class CourseLearningMaterial implements CourseLearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use MeshDescriptorsEntity;
    use SortableEntity;
    use LearningMaterialRelationshipEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'course_learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     */
    #[ORM\Column(name: 'notes', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    protected $notes;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'required', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $required;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'notes_are_public', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $publicNotes;

    /**
     * @var CourseInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $course;

    /**
     * @var LearningMaterialInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterial', inversedBy: 'courseLearningMaterials')]
    #[ORM\JoinColumn(name: 'learning_material_id', referencedColumnName: 'learning_material_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $learningMaterial;

    /**
     * @var ArrayCollection|MeshDescriptor[]
     *   joinColumns={
     *       name="course_learning_material_id",
     *       referencedColumnName="course_learning_material_id",
     *       onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *   }
     * )
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'courseLearningMaterials')]
    #[ORM\JoinTable(name: 'course_learning_material_x_mesh')]
    #[ORM\JoinColumn(
        name: 'course_learning_material_id',
        referencedColumnName: 'course_learning_material_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\InverseJoinColumn(
        name: 'mesh_descriptor_uid',
        referencedColumnName: 'mesh_descriptor_uid',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $meshDescriptors;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    protected $position;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $startDate;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'end_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $endDate;

    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
        $this->publicNotes = false;
        $this->required = false;
        $this->position = 0;
    }

    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    public function getCourse(): CourseInterface
    {
        return $this->course;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->course];
    }
}
