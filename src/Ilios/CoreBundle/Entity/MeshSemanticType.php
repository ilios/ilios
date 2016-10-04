<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class MeshSemanticType
 * @package Ilios\CoreBundle\Entity
 * @deprecated
 *
 * @ORM\Table(name="mesh_semantic_type")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class MeshSemanticType implements MeshSemanticTypeInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="mesh_semantic_type_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 192
     * )
     *
     * @ORM\Column(type="string", length=192)
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $name;

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
     * @ORM\ManyToMany(targetEntity="MeshConcept", mappedBy="semanticTypes")
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
     * @param Collection $concepts
     */
    public function setConcepts(Collection $concepts)
    {
        $this->concepts = new ArrayCollection();

        foreach ($concepts as $concept) {
            $this->addConcept($concept);
        }
    }

    /**
     * @param MeshConceptInterface $concept
     */
    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->addSemanticType($this);
        }
    }

    /**
     * @param MeshConceptInterface $concept
     */
    public function removeConcept(MeshConceptInterface $concept)
    {
        $this->concepts->removeElement($concept);
        $concept->removeSemanticType($this);
    }

    /**
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function getConcepts()
    {
        return $this->concepts;
    }
}
