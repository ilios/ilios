<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\NameableEntity;

/**
 * Class MeshSemanticType
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_semantic_type")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class MeshSemanticType implements MeshSemanticTypeInterface
{
    use NameableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var string
     *
     * @ORM\Column(name="mesh_semantic_type_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $uuid;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=192)
    */
    protected $name;

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
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->meshSemanticTypeUid = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->meshSemanticTypeUid : $this->uuid;
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
