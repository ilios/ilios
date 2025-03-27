<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use App\Traits\IdentifiableStringEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\TimestampableEntity;
use App\Repository\MeshConceptRepository;

#[ORM\Table(name: 'mesh_concept')]
#[ORM\Entity(repositoryClass: MeshConceptRepository::class)]
#[IA\Entity]
class MeshConcept implements MeshConceptInterface
{
    use IdentifiableStringEntity;
    use NameableEntity;
    use TimestampableEntity;
    use CreatedAtEntity;

    #[ORM\Column(name: 'mesh_concept_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    protected string $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    protected string $name;

    #[ORM\Column(name: 'preferred', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $preferred;

    #[ORM\Column(name: 'scope_note', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $scopeNote = null;

    #[ORM\Column(name: 'casn_1_name', type: 'string', length: 512, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 512)]
    protected ?string $casn1Name = null;

    #[ORM\ManyToMany(targetEntity: 'MeshTerm', mappedBy: 'concepts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

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

    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'concepts')]
    #[ORM\JoinTable(name: 'mesh_descriptor_x_concept')]
    #[ORM\JoinColumn(name: 'mesh_concept_uid', referencedColumnName: 'mesh_concept_uid')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descriptors;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
        $this->createdAt = new DateTime();
        $this->preferred = false;
        $this->terms = new ArrayCollection();
        $this->descriptors = new ArrayCollection();
    }

    public function setPreferred(bool $preferred): void
    {
        $this->preferred = $preferred;
    }

    public function getPreferred(): bool
    {
        return $this->preferred;
    }

    public function setScopeNote(?string $scopeNote): void
    {
        $this->scopeNote = $scopeNote;
    }

    public function getScopeNote(): ?string
    {
        return $this->scopeNote;
    }

    public function setCasn1Name(?string $casn1Name): void
    {
        $this->casn1Name = $casn1Name;
    }

    public function getCasn1Name(): ?string
    {
        return $this->casn1Name;
    }

    public function setTerms(Collection $terms): void
    {
        $this->terms = new ArrayCollection();

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    public function addTerm(MeshTermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addConcept($this);
        }
    }

    public function removeTerm(MeshTermInterface $term): void
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeConcept($this);
        }
    }

    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function setDescriptors(Collection $descriptors): void
    {
        $this->descriptors = new ArrayCollection();

        foreach ($descriptors as $descriptor) {
            $this->addDescriptor($descriptor);
        }
    }

    public function addDescriptor(MeshDescriptorInterface $descriptor): void
    {
        if (!$this->descriptors->contains($descriptor)) {
            $this->descriptors->add($descriptor);
        }
    }

    public function removeDescriptor(MeshDescriptorInterface $descriptor): void
    {
        $this->descriptors->removeElement($descriptor);
    }

    public function getDescriptors(): Collection
    {
        return $this->descriptors;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
