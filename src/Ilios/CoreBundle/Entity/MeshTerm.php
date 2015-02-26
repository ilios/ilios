<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

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
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="mesh_term_uid", type="string", length=9)
     * @ORM\Id
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 9
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=192)
     * @ORM\Id
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 192
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lexical_tag", type="string", length=12, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 12
     * )
     */
    protected $lexicalTag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="concept_preferred", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     */
    protected $conceptPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="record_preferred", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     */
    protected $recordPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permuted", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     */
    protected $permuted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="print", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     */
    protected $printable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Assert\NotBlank()
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
     * @param boolean $printable
     */
    public function setPrintable($printable)
    {
        $this->printable = $printable;
    }

    /**
     * @return boolean
     */
    public function isPrintable()
    {
        return $this->printable;
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
