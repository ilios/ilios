<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Repository\MeshConceptRepository;

/**
 * Class MeshConcept
 */
#[ORM\Table(name: 'mesh_concept')]
#[ORM\Entity(repositoryClass: MeshConceptRepository::class)]
#[IA\Entity]
class MeshConcept implements MeshConceptInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use CreatedAtEntity;

    /**
     * @var string
     * @Assert\Type(type="string")
     */
    #[ORM\Column(name: 'mesh_concept_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $name;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'preferred', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $preferred;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     */
    #[ORM\Column(name: 'scope_note', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $scopeNote;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=512)
     * })
     */
    #[ORM\Column(name: 'casn_1_name', type: 'string', length: 512, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $casn1Name;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=30)
     * })
     */
    #[ORM\Column(name: 'registry_number', type: 'string', length: 30, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $registryNumber;

    /**
     * @var ArrayCollection|MeshTermInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'MeshTerm', mappedBy: 'concepts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;


    #[ORM\Column(name: 'created_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $createdAt;


    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $updatedAt;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'concepts')]
    #[ORM\JoinTable(name: 'mesh_descriptor_x_concept')]
    #[ORM\JoinColumn(name: 'mesh_concept_uid', referencedColumnName: 'mesh_concept_uid')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $descriptors;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
        $this->createdAt = new DateTime();
        $this->preferred = false;
        $this->terms = new ArrayCollection();
        $this->descriptors = new ArrayCollection();
    }

    /**
     * @param bool $preferred
     */
    public function setPreferred($preferred)
    {
        $this->preferred = $preferred;
    }

    public function getPreferred(): bool
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

    public function getScopeNote(): string
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

    public function getCasn1Name(): string
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

    public function getRegistryNumber(): string
    {
        return $this->registryNumber;
    }

    public function setTerms(Collection $terms)
    {
        $this->terms = new ArrayCollection();

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    public function addTerm(MeshTermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addConcept($this);
        }
    }

    public function removeTerm(MeshTermInterface $term)
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeConcept($this);
        }
    }

    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function setDescriptors(Collection $descriptors)
    {
        $this->descriptors = new ArrayCollection();

        foreach ($descriptors as $descriptor) {
            $this->addDescriptor($descriptor);
        }
    }

    public function addDescriptor(MeshDescriptorInterface $descriptor)
    {
        if (!$this->descriptors->contains($descriptor)) {
            $this->descriptors->add($descriptor);
        }
    }

    public function removeDescriptor(MeshDescriptorInterface $descriptor)
    {
        $this->descriptors->removeElement($descriptor);
    }

    public function getDescriptors(): Collection
    {
        return $this->descriptors;
    }
}
