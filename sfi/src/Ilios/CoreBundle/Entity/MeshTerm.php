<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableUuidEntity;

/**
 * Class MeshTerm
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_term")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class MeshTerm implements MeshTermInterface
{
    use NameableEntity;
    use StringableUuidEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="mesh_term_uid", type="string", length=9)
     * @ORM\Id
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("id")
     */
    protected $uuid;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=192)
    * @ORM\Id
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lexical_tag", type="string", length=12, nullable=true)
     */
    protected $lexicalTag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="concept_preferred", type="boolean", nullable=true)
     */
    protected $conceptPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="record_preferred", type="boolean", nullable=true)
     */
    protected $recordPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permuted", type="boolean", nullable=true)
     */
    protected $permuted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="print", type="boolean", nullable=true)
     */
    protected $print;

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

    // /**
    //  * @var ArrayCollection|MeshConceptInterface[]
    //  *
    //  * @ORM\ManyToMany(targetEntity="MeshConcept", mappedBy="meshTerms")
    //  *
    //  * @JMS\Expose
    //  * @JMS\Type("array<string>")
    //  */
    // protected $meshConcepts;

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->meshTermUid = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->meshTermUid : $this->uuid;
    }

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag)
    {
        $this->lexicalTag = $lexicalTag;
    }

    /**
     * @return string
     */
    public function getLexicalTag()
    {
        return $this->lexicalTag;
    }

    /**
     * @param boolean $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred)
    {
        $this->conceptPreferred = $conceptPreferred;
    }

    /**
     * @return boolean
     */
    public function isConceptPreferred()
    {
        return $this->conceptPreferred;
    }

    /**
     * @param boolean $recordPreferred
     */
    public function setRecordPreferred($recordPreferred)
    {
        $this->recordPreferred = $recordPreferred;
    }

    /**
     * @return boolean
     */
    public function isRecordPreferred()
    {
        return $this->recordPreferred;
    }

    /**
     * @param boolean $permuted
     */
    public function setPermuted($permuted)
    {
        $this->permuted = $permuted;
    }

    /**
     * @return boolean
     */
    public function isPermuted()
    {
        return $this->permuted;
    }

    /**
     * @param boolean $print
     */
    public function setPrint($print)
    {
        $this->print = $print;
    }

    /**
     * @return boolean
     */
    public function hasPrint()
    {
        return $this->print;
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
