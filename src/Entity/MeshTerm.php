<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ConceptsEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Repository\MeshTermRepository;

/**
 * Class MeshTerm
 */
#[ORM\Table(name: 'mesh_term')]
#[ORM\UniqueConstraint(name: 'mesh_term_uid_name', columns: ['mesh_term_uid', 'name'])]
#[ORM\Entity(repositoryClass: MeshTermRepository::class)]
#[IA\Entity]
class MeshTerm implements MeshTermInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use ConceptsEntity;
    use CreatedAtEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'mesh_term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'mesh_term_uid', type: 'string', length: 12)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 12)]
    protected $meshTermUid;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'lexical_tag', type: 'string', length: 12, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 12),
    ])]
    protected $lexicalTag;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'concept_preferred', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $conceptPreferred;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'record_preferred', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $recordPreferred;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'permuted', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $permuted;


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
     * @var ArrayCollection|MeshConceptInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'MeshConcept', inversedBy: 'terms')]
    #[ORM\JoinTable(name: 'mesh_concept_x_term')]
    #[ORM\JoinColumn(name: 'mesh_term_id', referencedColumnName: 'mesh_term_id')]
    #[ORM\InverseJoinColumn(name: 'mesh_concept_uid', referencedColumnName: 'mesh_concept_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $concepts;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->concepts = new ArrayCollection();
    }

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid)
    {
        $this->meshTermUid = $meshTermUid;
    }

    public function getMeshTermUid(): string
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

    public function getLexicalTag(): string
    {
        return $this->lexicalTag;
    }

    /**
     * @param bool $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred)
    {
        $this->conceptPreferred = $conceptPreferred;
    }

    public function isConceptPreferred(): bool
    {
        return $this->conceptPreferred;
    }

    /**
     * @param bool $recordPreferred
     */
    public function setRecordPreferred($recordPreferred)
    {
        $this->recordPreferred = $recordPreferred;
    }

    public function isRecordPreferred(): bool
    {
        return $this->recordPreferred;
    }

    /**
     * @param bool $permuted
     */
    public function setPermuted($permuted)
    {
        $this->permuted = $permuted;
    }

    public function isPermuted(): bool
    {
        return $this->permuted;
    }
}
