<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\MeshTreeRepository;

#[ORM\Table(name: 'mesh_tree')]
#[ORM\Entity(repositoryClass: MeshTreeRepository::class)]
#[IA\Entity]
class MeshTree implements MeshTreeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'mesh_tree_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'tree_number', type: 'string', length: 80)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 80)]
    protected string $treeNumber;

    #[ORM\ManyToOne(targetEntity: 'MeshDescriptor', inversedBy: 'trees')]
    #[ORM\JoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected MeshDescriptorInterface $descriptor;

    public function setTreeNumber(string $treeNumber)
    {
        $this->treeNumber = $treeNumber;
    }

    public function getTreeNumber(): string
    {
        return $this->treeNumber;
    }

    public function setDescriptor(MeshDescriptorInterface $descriptor): MeshTree
    {
        $this->descriptor = $descriptor;

        return $this;
    }

    public function getDescriptor(): MeshDescriptorInterface
    {
        return $this->descriptor;
    }
}
