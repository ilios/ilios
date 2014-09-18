<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshSemanticType
 */
class MeshSemanticType
{
    /**
     * @var string
     */
    private $meshSemanticTypeUid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * Set meshSemanticTypeUid
     *
     * @param string $meshSemanticTypeUid
     * @return MeshSemanticType
     */
    public function setMeshSemanticTypeUid($meshSemanticTypeUid)
    {
        $this->meshSemanticTypeUid = $meshSemanticTypeUid;

        return $this;
    }

    /**
     * Get meshSemanticTypeUid
     *
     * @return string 
     */
    public function getMeshSemanticTypeUid()
    {
        return $this->meshSemanticTypeUid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MeshSemanticType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MeshSemanticType
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return MeshSemanticType
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
