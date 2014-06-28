<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshQualifier
 */
class MeshQualifier
{
    /**
     * @var string
     */
    private $meshQualifierUid;

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
     * Set meshQualifierUid
     *
     * @param string $meshQualifierUid
     * @return MeshQualifier
     */
    public function setMeshQualifierUid($meshQualifierUid)
    {
        $this->meshQualifierUid = $meshQualifierUid;

        return $this;
    }

    /**
     * Get meshQualifierUid
     *
     * @return string 
     */
    public function getMeshQualifierUid()
    {
        return $this->meshQualifierUid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MeshQualifier
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
     * @return MeshQualifier
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
     * @return MeshQualifier
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
