<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class MeshPreviousIndexing
 *
 * @ORM\Table(name="mesh_previous_indexing",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="descriptor_previous", columns={"mesh_descriptor_uid"})
 *  })
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\MeshPreviousIndexingRepository")
 *
 * @IS\Entity
 */
class MeshPreviousIndexing implements MeshPreviousIndexingInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="mesh_previous_indexing_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var MeshDescriptorInterface
     *
     * @ORM\OneToOne(targetEntity="MeshDescriptor", inversedBy="previousIndexing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", unique=true)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $descriptor;

    /**
     * @var string
     *
     * @ORM\Column(name="previous_indexing", type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
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
