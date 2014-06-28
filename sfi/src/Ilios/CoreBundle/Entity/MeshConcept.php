<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshConcept
 */
class MeshConcept
{
    /**
     * @var string
     */
    private $meshConceptUid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $umlsUid;

    /**
     * @var boolean
     */
    private $preferred;

    /**
     * @var string
     */
    private $scopeNote;

    /**
     * @var string
     */
    private $casn1Name;

    /**
     * @var string
     */
    private $registryNumber;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * Set meshConceptUid
     *
     * @param string $meshConceptUid
     * @return MeshConcept
     */
    public function setMeshConceptUid($meshConceptUid)
    {
        $this->meshConceptUid = $meshConceptUid;

        return $this;
    }

    /**
     * Get meshConceptUid
     *
     * @return string 
     */
    public function getMeshConceptUid()
    {
        return $this->meshConceptUid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MeshConcept
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
     * Set umlsUid
     *
     * @param string $umlsUid
     * @return MeshConcept
     */
    public function setUmlsUid($umlsUid)
    {
        $this->umlsUid = $umlsUid;

        return $this;
    }

    /**
     * Get umlsUid
     *
     * @return string 
     */
    public function getUmlsUid()
    {
        return $this->umlsUid;
    }

    /**
     * Set preferred
     *
     * @param boolean $preferred
     * @return MeshConcept
     */
    public function setPreferred($preferred)
    {
        $this->preferred = $preferred;

        return $this;
    }

    /**
     * Get preferred
     *
     * @return boolean 
     */
    public function getPreferred()
    {
        return $this->preferred;
    }

    /**
     * Set scopeNote
     *
     * @param string $scopeNote
     * @return MeshConcept
     */
    public function setScopeNote($scopeNote)
    {
        $this->scopeNote = $scopeNote;

        return $this;
    }

    /**
     * Get scopeNote
     *
     * @return string 
     */
    public function getScopeNote()
    {
        return $this->scopeNote;
    }

    /**
     * Set casn1Name
     *
     * @param string $casn1Name
     * @return MeshConcept
     */
    public function setCasn1Name($casn1Name)
    {
        $this->casn1Name = $casn1Name;

        return $this;
    }

    /**
     * Get casn1Name
     *
     * @return string 
     */
    public function getCasn1Name()
    {
        return $this->casn1Name;
    }

    /**
     * Set registryNumber
     *
     * @param string $registryNumber
     * @return MeshConcept
     */
    public function setRegistryNumber($registryNumber)
    {
        $this->registryNumber = $registryNumber;

        return $this;
    }

    /**
     * Get registryNumber
     *
     * @return string 
     */
    public function getRegistryNumber()
    {
        return $this->registryNumber;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MeshConcept
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
     * @return MeshConcept
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
