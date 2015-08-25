<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class MeshConcept
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_concept")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class MeshConcept implements MeshConceptInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;


    /**
     * @var string
     *
     * @ORM\Column(name="mesh_concept_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\Type(type="string")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $id;

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
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="umls_uid", type="string", length=9)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 9
     * )
     */
    protected $umlsUid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="preferred", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    protected $preferred;

    /**
     * @var string
     *
     * @ORM\Column(name="scope_note", type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     */
    protected $scopeNote;

    /**
     * @var string
     *
     * @ORM\Column(name="casn_1_name", type="string", length=127, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 127
     * )
     */
    protected $casn1Name;

    /**
     * @var string
     *
     * @ORM\Column(name="registry_number", type="string", length=30, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     */
    protected $registryNumber;

    /**
     * @var ArrayCollection|MeshSemanticTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshSemanticType", inversedBy="concepts")
     * @ORM\JoinTable(name="mesh_concept_x_semantic_type",
     *   joinColumns={
     *     @ORM\JoinColumn(name="mesh_concept_uid", referencedColumnName="mesh_concept_uid")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_semantic_type_uid", referencedColumnName="mesh_semantic_type_uid")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("semanticTypes")
     */
    protected $semanticTypes;

    /**
     * @var ArrayCollection|MeshTermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshTerm", mappedBy="concepts")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $terms;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Assert\NotBlank()
     *
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

    /**
    * @var ArrayCollection|MeshDescriptorInterface[]
    *
    * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="concepts")
    * @ORM\JoinTable(name="mesh_descriptor_x_concept",
    *   joinColumns={
    *     @ORM\JoinColumn(name="mesh_concept_uid", referencedColumnName="mesh_concept_uid")
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
     * Constructor
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->preferred = false;
    }

    /**
     * @param string $umlsUid
     */
    public function setUmlsUid($umlsUid)
    {
        $this->umlsUid = $umlsUid;
    }

    /**
     * @return string
     */
    public function getUmlsUid()
    {
        return $this->umlsUid;
    }

    /**
     * @param boolean $preferred
     */
    public function setPreferred($preferred)
    {
        $this->preferred = $preferred;
    }

    /**
     * @return boolean
     */
    public function getPreferred()
    {
        return $this->preferred;
    }

    /**
     * @param string $scopeNote
     */
    public function setScopeNote($scopeNote)
    {
        $this->scopeNote = $scopeNote;
    }

    /**
     * @return string
     */
    public function getScopeNote()
    {
        return $this->scopeNote;
    }

    /**
     * @param string $casn1Name
     */
    public function setCasn1Name($casn1Name)
    {
        $this->casn1Name = $casn1Name;
    }

    /**
     * @return string
     */
    public function getCasn1Name()
    {
        return $this->casn1Name;
    }

    /**
     * @param string $registryNumber
     */
    public function setRegistryNumber($registryNumber)
    {
        $this->registryNumber = $registryNumber;
    }

    /**
     * @return string
     */
    public function getRegistryNumber()
    {
        return $this->registryNumber;
    }
}
