<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ConceptsEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class MeshTerm
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(
 *  name="mesh_term",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="mesh_term_uid_name", columns={"mesh_term_uid","name"})
 *  }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class MeshTerm implements MeshTermInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use ConceptsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="mesh_term_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mesh_term_uid", type="string", length=9)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 9
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("meshTermUid")
     */
    protected $meshTermUid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=192)
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
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("lexicalTag")
     */
    protected $lexicalTag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="concept_preferred", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("conceptPreferred")
     */
    protected $conceptPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="record_preferred", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("recordPreferred")
     */
    protected $recordPreferred;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permuted", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $permuted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="print", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $printable;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection|MeshConceptInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshConcept", inversedBy="terms")
     * @ORM\JoinTable(name="mesh_concept_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="mesh_term_id", referencedColumnName="mesh_term_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_concept_uid", referencedColumnName="mesh_concept_uid")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $concepts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->concepts = new ArrayCollection();
    }

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid)
    {
        $this->meshTermUid = $meshTermUid;
    }

    /**
     * @return string
     */
    public function getMeshTermUid()
    {
        return $this->meshTermUid;
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
}
