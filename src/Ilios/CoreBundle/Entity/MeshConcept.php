<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class MeshConcept
 *
 * @ORM\Table(name="mesh_concept")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\MeshConceptRepository")
 *
 * @IS\Entity
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
     * @ORM\Column(name="mesh_concept_uid", type="string", length=12)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\Type(type="string")
     *
     * @IS\Expose
     * @IS\Type("string")
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
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="preferred", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
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
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $scopeNote;

    /**
     * @var string
     *
     * @ORM\Column(name="casn_1_name", type="string", length=512, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 512
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
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
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $registryNumber;

    /**
     * @var ArrayCollection|MeshTermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshTerm", mappedBy="concepts")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $terms;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
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
    * @IS\Expose
    * @IS\Type("entityCollection")
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
        $this->terms = new ArrayCollection();
        $this->descriptors = new ArrayCollection();
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
     * @param Collection $terms
     */
    public function setTerms(Collection $terms)
    {
        $this->terms = new ArrayCollection();

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    /**
     * @param MeshTermInterface $term
     */
    public function addTerm(MeshTermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addConcept($this);
        }
    }

    /**
     * @param MeshTermInterface $term
     */
    public function removeTerm(MeshTermInterface $term)
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeConcept($this);
        }
    }

    /**
     * @return ArrayCollection|MeshTermInterface[]
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * @param Collection $descriptors
     */
    public function setDescriptors(Collection $descriptors)
    {
        $this->descriptors = new ArrayCollection();

        foreach ($descriptors as $descriptor) {
            $this->addDescriptor($descriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function addDescriptor(MeshDescriptorInterface $descriptor)
    {
        if (!$this->descriptors->contains($descriptor)) {
            $this->descriptors->add($descriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function removeDescriptor(MeshDescriptorInterface $descriptor)
    {
        $this->descriptors->removeElement($descriptor);
    }

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getDescriptors()
    {
        return $this->descriptors;
    }
}
