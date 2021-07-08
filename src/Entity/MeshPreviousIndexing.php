<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\MeshPreviousIndexingRepository;

/**
 * Class MeshPreviousIndexing
 *  uniqueConstraints={
 *  })
 * @IS\Entity
 */
#[ORM\Table(name: 'mesh_previous_indexing')]
#[ORM\UniqueConstraint(name: 'descriptor_previous', columns: ['mesh_descriptor_uid'])]
#[ORM\Entity(repositoryClass: MeshPreviousIndexingRepository::class)]
class MeshPreviousIndexing implements MeshPreviousIndexingInterface
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
    #[ORM\Column(name: 'mesh_previous_indexing_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var MeshDescriptorInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'MeshDescriptor', inversedBy: 'previousIndexing')]
    #[ORM\JoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', unique: true)]
    protected $descriptor;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'previous_indexing', type: 'text')]
    protected $previousIndexing;
    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function setDescriptor(MeshDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }
    /**
     * @return MeshDescriptorInterface
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }
    /**
     * @param string $previousIndexing
     */
    public function setPreviousIndexing($previousIndexing)
    {
        $this->previousIndexing = $previousIndexing;
    }
    /**
     * @return string
     */
    public function getPreviousIndexing()
    {
        return $this->previousIndexing;
    }
}
