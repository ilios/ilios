<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\MeshPreviousIndexingRepository;

#[ORM\Table(name: 'mesh_previous_indexing')]
#[ORM\UniqueConstraint(name: 'descriptor_previous', columns: ['mesh_descriptor_uid'])]
#[ORM\Entity(repositoryClass: MeshPreviousIndexingRepository::class)]
#[IA\Entity]
class MeshPreviousIndexing implements MeshPreviousIndexingInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'mesh_previous_indexing_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\OneToOne(inversedBy: 'previousIndexing', targetEntity: 'MeshDescriptor')]
    #[ORM\JoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', unique: true)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected MeshDescriptorInterface $descriptor;

    #[ORM\Column(name: 'previous_indexing', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $previousIndexing;

    public function setDescriptor(MeshDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    public function getDescriptor(): MeshDescriptorInterface
    {
        return $this->descriptor;
    }

    public function setPreviousIndexing(string $previousIndexing)
    {
        $this->previousIndexing = $previousIndexing;
    }

    public function getPreviousIndexing(): string
    {
        return $this->previousIndexing;
    }
}
