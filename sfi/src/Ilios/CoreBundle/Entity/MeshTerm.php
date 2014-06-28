<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshTerm
 */
class MeshTerm
{
    /**
     * @var string
     */
    private $meshTermUid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lexicalTag;

    /**
     * @var boolean
     */
    private $conceptPreferred;

    /**
     * @var boolean
     */
    private $recordPreferred;

    /**
     * @var boolean
     */
    private $permuted;

    /**
     * @var boolean
     */
    private $print;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * Set meshTermUid
     *
     * @param string $meshTermUid
     * @return MeshTerm
     */
    public function setMeshTermUid($meshTermUid)
    {
        $this->meshTermUid = $meshTermUid;

        return $this;
    }

    /**
     * Get meshTermUid
     *
     * @return string 
     */
    public function getMeshTermUid()
    {
        return $this->meshTermUid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MeshTerm
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
     * Set lexicalTag
     *
     * @param string $lexicalTag
     * @return MeshTerm
     */
    public function setLexicalTag($lexicalTag)
    {
        $this->lexicalTag = $lexicalTag;

        return $this;
    }

    /**
     * Get lexicalTag
     *
     * @return string 
     */
    public function getLexicalTag()
    {
        return $this->lexicalTag;
    }

    /**
     * Set conceptPreferred
     *
     * @param boolean $conceptPreferred
     * @return MeshTerm
     */
    public function setConceptPreferred($conceptPreferred)
    {
        $this->conceptPreferred = $conceptPreferred;

        return $this;
    }

    /**
     * Get conceptPreferred
     *
     * @return boolean 
     */
    public function getConceptPreferred()
    {
        return $this->conceptPreferred;
    }

    /**
     * Set recordPreferred
     *
     * @param boolean $recordPreferred
     * @return MeshTerm
     */
    public function setRecordPreferred($recordPreferred)
    {
        $this->recordPreferred = $recordPreferred;

        return $this;
    }

    /**
     * Get recordPreferred
     *
     * @return boolean 
     */
    public function getRecordPreferred()
    {
        return $this->recordPreferred;
    }

    /**
     * Set permuted
     *
     * @param boolean $permuted
     * @return MeshTerm
     */
    public function setPermuted($permuted)
    {
        $this->permuted = $permuted;

        return $this;
    }

    /**
     * Get permuted
     *
     * @return boolean 
     */
    public function getPermuted()
    {
        return $this->permuted;
    }

    /**
     * Set print
     *
     * @param boolean $print
     * @return MeshTerm
     */
    public function setPrint($print)
    {
        $this->print = $print;

        return $this;
    }

    /**
     * Get print
     *
     * @return boolean 
     */
    public function getPrint()
    {
        return $this->print;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MeshTerm
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
     * @return MeshTerm
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
