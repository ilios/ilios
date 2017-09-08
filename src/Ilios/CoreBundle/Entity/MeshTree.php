<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class MeshTree
 *
 * @ORM\Table(name="mesh_tree")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\MeshTreeRepository")
 *
 * @IS\Entity
 */
class MeshTree implements MeshTreeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="mesh_tree_id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="tree_number", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $treeNumber;

    /**
     * @var MeshDescriptorInterface
     *
     * @ORM\ManyToOne(targetEntity="MeshDescriptor", inversedBy="trees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
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
