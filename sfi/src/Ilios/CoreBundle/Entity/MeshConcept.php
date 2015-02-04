<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;
use Ilios\CoreBundle\Traits\StringableUuidEntity;

/**
 * Class MeshConcept
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_concept")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class MeshConcept implements MeshConceptInterface
{
//    use UniversallyUniqueEntity;
    use NameableEntity;
    use StringableUuidEntity;


    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var string
     *
     * @ORM\Column(name="mesh_concept_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="umls_uid", type="string", length=9)
     */
    protected $umlsUid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="preferred", type="boolean")
     */
    protected $preferred;

    /**
     * @var string
     *
     * @ORM\Column(name="scope_note", type="text", nullable=true)
     */
    protected $scopeNote;

    /**
     * @var string
     *
     * @ORM\Column(name="casn_1_name", type="string", length=127, nullable=true)
     */
    protected $casn1Name;

    /**
     * @var string
     *
     * @ORM\Column(name="registry_number", type="string", length=30, nullable=true)
     */
    protected $registryNumber;

    // /**
    //  * @var ArrayCollection|MeshTermInterface[]
    //  *
    //  * @ORM\ManyToMany(targetEntity="MeshTerm", inversedBy="meshConcepts")
    //  * @ORM\JoinTable(name="mesh_concept_x_term",
    //  *   joinColumns={
    //  *     @ORM\JoinColumn(name="mesh_concept_uid", referencedColumnName="mesh_concept_uid")
    //  *   },
    //  *   inverseJoinColumns={
    //  *     @ORM\JoinColumn(name="mesh_term_uid", referencedColumnName="mesh_term_uid")
    //  *   }
    //  * )
    //  *
    //  * @JMS\Expose
    //  * @JMS\Type("array<string>")
    //  * @JMS\SerializedName("meshTerms")
    //  */
    // protected $meshTerms;

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
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->meshConceptUid = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->meshConceptUid : $this->uuid;
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
