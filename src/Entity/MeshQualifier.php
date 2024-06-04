<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use App\Traits\IdentifiableStringEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\NameableEntity;
use App\Traits\TimestampableEntity;
use App\Repository\MeshQualifierRepository;

#[ORM\Table(name: 'mesh_qualifier')]
#[ORM\Entity(repositoryClass: MeshQualifierRepository::class)]
#[IA\Entity]
class MeshQualifier implements MeshQualifierInterface
{
    use IdentifiableStringEntity;
    use TimestampableEntity;
    use NameableEntity;
    use CreatedAtEntity;

    #[ORM\Column(name: 'mesh_qualifier_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    protected string $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $name;

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

    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'qualifiers')]
    #[ORM\JoinTable(name: 'mesh_descriptor_x_qualifier')]
    #[ORM\JoinColumn(name: 'mesh_qualifier_uid', referencedColumnName: 'mesh_qualifier_uid')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descriptors;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->descriptors = new ArrayCollection();
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
