<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearningMaterialRelationshipEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SessionConsolidationEntity;
use App\Traits\SortableEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SessionLearningMaterialRepository;

/**
 * Class SessionLearningMaterial
 */
#[ORM\Table(name: 'session_learning_material')]
#[ORM\Index(columns: ['session_id', 'learning_material_id'], name: 'session_lm_k')]
#[ORM\Index(columns: ['learning_material_id'], name: 'learning_material_id_k')]
#[ORM\Index(columns: ['session_id'], name: 'IDX_9BE2AF8D613FECDF')]
#[ORM\Entity(repositoryClass: SessionLearningMaterialRepository::class)]
#[IA\Entity]
class SessionLearningMaterial implements SessionLearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;
    use MeshDescriptorsEntity;
    use SortableEntity;
    use LearningMaterialRelationshipEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'session_learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
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
     * @var SessionInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $session;

    /**
     * @var LearningMaterialInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterial', inversedBy: 'sessionLearningMaterials')]
    #[ORM\JoinColumn(name: 'learning_material_id', referencedColumnName: 'learning_material_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $learningMaterial;

    /**
     * @var MeshDescriptorInterface
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'sessionLearningMaterials')]
    #[ORM\JoinTable(name: 'session_learning_material_x_mesh')]
    #[ORM\JoinColumn(
        name: 'session_learning_material_id',
        referencedColumnName: 'session_learning_material_id',
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
     * @var \DateTime
     */
    #[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $startDate;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'end_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $endDate;

    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
        $this->publicNotes = false;
        $this->position = 0;
    }

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
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
