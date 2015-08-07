<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class MeshPreviousIndexing
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_previous_indexing")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class MeshPreviousIndexing implements MeshPreviousIndexingInterface
{
    /**
     * @var MeshDescriptorInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="MeshDescriptor", inversedBy="previousIndexing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", unique=true)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->descriptor;
    }
}
