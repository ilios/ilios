<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\MeshTreeRepository;

/**
 * Class MeshTree
 * @IS\Entity
 */
#[ORM\Table(name: 'mesh_tree')]
#[ORM\Entity(repositoryClass: MeshTreeRepository::class)]
class MeshTree implements MeshTreeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'mesh_tree_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'tree_number', type: 'string', length: 80)]
    protected $treeNumber;

    /**
     * @var MeshDescriptorInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'MeshDescriptor', inversedBy: 'trees')]
    #[ORM\JoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    protected $descriptor;

    /**
     * Set treeNumber
     *
     * @param string $treeNumber
     *
     * @return MeshTree
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
     * @param MeshDescriptorInterface $descriptor
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
