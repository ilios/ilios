<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;
use Ilios\CoreBundle\Traits\StringableUuidEntity;

/**
 * Class MeshQualifier
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_qualifier")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class MeshQualifier implements MeshQualifierInterface
{
//    use UniversallyUniqueEntity;
//    use TimestampableEntity;
    use NameableEntity;
    use StringableUuidEntity;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=60)
    */
    protected $name;

    /**
     * @deprecated Replace with trait.
     * @var string
     *
     * @ORM\Column(name="mesh_qualifier_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $uuid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
    * @var ArrayCollection|MeshDescriptorInterface[]
    *
    * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="qualifiers")
    * @ORM\JoinTable(name="mesh_descriptor_x_qualifier",
    *   joinColumns={
    *     @ORM\JoinColumn(name="mesh_qualifier_uid", referencedColumnName="mesh_qualifier_uid")
    *   },
    *   inverseJoinColumns={
    *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid")
    *   }
    * )
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $descriptors;

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->meshQualifierUid = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->meshQualifierUid : $this->uuid;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
