<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TimestampableEntity;
use App\Repository\MeshTermRepository;
use Override;

#[ORM\Table(name: 'mesh_term')]
#[ORM\UniqueConstraint(name: 'mesh_term_uid_name', columns: ['mesh_term_uid', 'name'])]
#[ORM\Entity(repositoryClass: MeshTermRepository::class)]
#[IA\Entity]
class MeshTerm implements MeshTermInterface
{
    use TimestampableEntity;

    #[ORM\Column(name: 'mesh_term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'mesh_term_uid', type: 'string', length: 12)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 12)]
    protected string $meshTermUid;

    #[ORM\Column(type: 'string', length: 255)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    protected string $name;

    #[ORM\Column(name: 'lexical_tag', type: 'string', length: 12, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 12)]
    protected ?string $lexicalTag = null;

    #[ORM\Column(name: 'concept_preferred', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $conceptPreferred = null;

    #[ORM\Column(name: 'record_preferred', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $recordPreferred = null;

    #[ORM\Column(name: 'permuted', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $permuted = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected DateTime $updatedAt;

    #[ORM\ManyToMany(targetEntity: MeshConcept::class, inversedBy: 'terms')]
    #[ORM\JoinTable(name: 'mesh_concept_x_term')]
    #[ORM\JoinColumn(name: 'mesh_term_id', referencedColumnName: 'mesh_term_id')]
    #[ORM\InverseJoinColumn(name: 'mesh_concept_uid', referencedColumnName: 'mesh_concept_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $concepts;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->concepts = new ArrayCollection();
    }

    #[Override]
    public function setMeshTermUid(string $meshTermUid): void
    {
        $this->meshTermUid = $meshTermUid;
    }

    #[Override]
    public function getMeshTermUid(): string
    {
        return $this->meshTermUid;
    }

    #[Override]
    public function setLexicalTag(?string $lexicalTag): void
    {
        $this->lexicalTag = $lexicalTag;
    }

    #[Override]
    public function getLexicalTag(): ?string
    {
        return $this->lexicalTag;
    }

    #[Override]
    public function setConceptPreferred(?bool $conceptPreferred): void
    {
        $this->conceptPreferred = $conceptPreferred;
    }

    #[Override]
    public function isConceptPreferred(): ?bool
    {
        return $this->conceptPreferred;
    }

    #[Override]
    public function setRecordPreferred(?bool $recordPreferred): void
    {
        $this->recordPreferred = $recordPreferred;
    }

    #[Override]
    public function isRecordPreferred(): ?bool
    {
        return $this->recordPreferred;
    }

    #[Override]
    public function setPermuted(?bool $permuted): void
    {
        $this->permuted = $permuted;
    }

    #[Override]
    public function isPermuted(): ?bool
    {
        return $this->permuted;
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
    public function setConcepts(Collection $concepts): void
    {
        $this->concepts = new ArrayCollection();

        foreach ($concepts as $concept) {
            $this->addConcept($concept);
        }
    }

    #[Override]
    public function addConcept(MeshConceptInterface $concept): void
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
        }
    }

    #[Override]
    public function removeConcept(MeshConceptInterface $concept): void
    {
        $this->concepts->removeElement($concept);
    }

    #[Override]
    public function getConcepts(): Collection
    {
        return $this->concepts;
    }

    #[Override]
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    #[Override]
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    #[Override]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
