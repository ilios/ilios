<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\MeshTreeRepository;

/**
 * Class MeshTree
 */
#[ORM\Table(name: 'mesh_tree')]
#[ORM\Entity(repositoryClass: MeshTreeRepository::class)]
#[IA\Entity]
class MeshTree implements MeshTreeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'mesh_tree_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     */
    #[ORM\Column(name: 'tree_number', type: 'string', length: 80)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $treeNumber;

    /**
     * @var MeshDescriptorInterface
     */
    #[ORM\ManyToOne(targetEntity: 'MeshDescriptor', inversedBy: 'trees')]
    #[ORM\JoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $descriptor;

    /**
     * Set treeNumber
     *
     * @param string $treeNumber
     */
    public function setTreeNumber($treeNumber)
    {
        $this->treeNumber = $treeNumber;
    }

    /**
     * Get treeNumber
     *
     * @return string
     */
    public function getTreeNumber()
    {
        return $this->treeNumber;
    }

    /**
     * Set meshDescriptor
     *
     *
     * @return MeshTree
     */
    public function setDescriptor(MeshDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;

        return $this;
    }

    /**
     * Get descriptor
     *
     * @return MeshDescriptorInterface
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }
}
