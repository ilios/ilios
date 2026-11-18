<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SortableEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\StringableIdEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SessionLearningMaterialRepository;
use Override;

#[ORM\Table(name: 'session_learning_material')]
#[ORM\Index(columns: ['session_id', 'learning_material_id'], name: 'session_lm_k')]
#[ORM\Index(columns: ['learning_material_id'], name: 'learning_material_id_k')]
#[ORM\Index(columns: ['session_id'], name: 'IDX_9BE2AF8D613FECDF')]
#[ORM\Entity(repositoryClass: SessionLearningMaterialRepository::class)]
#[IA\Entity]
class SessionLearningMaterial implements SessionLearningMaterialInterface
{
    use StringableIdEntity;
    use SortableEntity;

    #[ORM\Column(name: 'session_learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'notes', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $notes = null;

    #[ORM\Column(name: 'required', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $required;

    #[ORM\Column(name: 'notes_are_public', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $publicNotes;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionInterface $session;

    #[ORM\ManyToOne(targetEntity: LearningMaterial::class, inversedBy: 'sessionLearningMaterials')]
    #[ORM\JoinColumn(name: 'learning_material_id', referencedColumnName: 'learning_material_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected LearningMaterialInterface $learningMaterial;

    #[ORM\ManyToMany(targetEntity: MeshDescriptor::class, inversedBy: 'sessionLearningMaterials')]
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
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $meshDescriptors;

    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $position;

    #[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected ?DateTime $startDate = null;

    #[ORM\Column(name: 'end_date', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected ?DateTime $endDate = null;

    public function __construct()
    {
        $this->meshDescriptors = new ArrayCollection();
        $this->publicNotes = false;
        $this->position = 0;
    }

    #[Override]
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    #[Override]
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    #[Override]
    public function getIndexableCourses(): array
    {
        return [$this->session->getCourse()];
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    #[Override]
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    #[Override]
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    #[Override]
    public function isRequired(): bool
    {
        return $this->required;
    }

    #[Override]
    public function setPublicNotes(bool $publicNotes): void
    {
        $this->publicNotes = $publicNotes;
    }

    #[Override]
    public function hasPublicNotes(): bool
    {
        return $this->publicNotes;
    }

    #[Override]
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterial = $learningMaterial;
    }

    #[Override]
    public function getLearningMaterial(): LearningMaterialInterface
    {
        return $this->learningMaterial;
    }

    #[Override]
    public function setStartDate(?DateTime $startDate = null): void
    {
        $this->startDate = $startDate;
    }

    #[Override]
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    #[Override]
    public function setEndDate(?DateTime $endDate = null): void
    {
        $this->endDate = $endDate;
    }

    #[Override]
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    #[Override]
    public function setMeshDescriptors(Collection $meshDescriptors): void
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    #[Override]
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    #[Override]
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }

    #[Override]
    public function getMeshDescriptors(): Collection
    {
        return $this->meshDescriptors;
    }

    #[Override]
    public function getSessions(): Collection
    {
        $session = $this->getSession();
        return new ArrayCollection([$session]);
    }
}
