<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ConceptsEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Repository\MeshTermRepository;

#[ORM\Table(name: 'mesh_term')]
#[ORM\UniqueConstraint(name: 'mesh_term_uid_name', columns: ['mesh_term_uid', 'name'])]
#[ORM\Entity(repositoryClass: MeshTermRepository::class)]
#[IA\Entity]
class MeshTerm implements MeshTermInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use ConceptsEntity;
    use CreatedAtEntity;

    #[ORM\Column(name: 'mesh_term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

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

    #[ORM\ManyToMany(targetEntity: 'MeshConcept', inversedBy: 'terms')]
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

    public function setMeshTermUid(string $meshTermUid): void
    {
        $this->meshTermUid = $meshTermUid;
    }

    public function getMeshTermUid(): string
    {
        return $this->meshTermUid;
    }

    public function setLexicalTag(?string $lexicalTag): void
    {
        $this->lexicalTag = $lexicalTag;
    }

    public function getLexicalTag(): ?string
    {
        return $this->lexicalTag;
    }

    public function setConceptPreferred(?bool $conceptPreferred): void
    {
        $this->conceptPreferred = $conceptPreferred;
    }

    public function isConceptPreferred(): ?bool
    {
        return $this->conceptPreferred;
    }

    public function setRecordPreferred(?bool $recordPreferred): void
    {
        $this->recordPreferred = $recordPreferred;
    }

    public function isRecordPreferred(): ?bool
    {
        return $this->recordPreferred;
    }

    public function setPermuted(?bool $permuted): void
    {
        $this->permuted = $permuted;
    }

    public function isPermuted(): ?bool
    {
        return $this->permuted;
    }
}
