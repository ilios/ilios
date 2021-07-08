<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ConceptsEntity;
use App\Annotation as IS;
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
 * @IS\Entity
 */
#[ORM\Table(name: 'mesh_term')]
#[ORM\UniqueConstraint(name: 'mesh_term_uid_name', columns: ['mesh_term_uid', 'name'])]
#[ORM\Entity(repositoryClass: MeshTermRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'mesh_term_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 12
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'mesh_term_uid', type: 'string', length: 12)]
    protected $meshTermUid;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected $name;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=12)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'lexical_tag', type: 'string', length: 12, nullable: true)]
    protected $lexicalTag;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'concept_preferred', type: 'boolean', nullable: true)]
    protected $conceptPreferred;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'record_preferred', type: 'boolean', nullable: true)]
    protected $recordPreferred;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'permuted', type: 'boolean', nullable: true)]
    protected $permuted;

    /**
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    protected $createdAt;

    /**
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    protected $updatedAt;

    /**
     * @var ArrayCollection|MeshConceptInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshConcept', inversedBy: 'terms')]
    #[ORM\JoinTable(name: 'mesh_concept_x_term')]
    #[ORM\JoinColumn(name: 'mesh_term_id', referencedColumnName: 'mesh_term_id')]
    #[ORM\InverseJoinColumn(name: 'mesh_concept_uid', referencedColumnName: 'mesh_concept_uid')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $concepts;

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
     * @param bool $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred)
    {
        $this->conceptPreferred = $conceptPreferred;
    }

    /**
     * @return bool
     */
    public function isConceptPreferred()
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

    /**
     * @return bool
     */
    public function isRecordPreferred()
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

    /**
     * @return bool
     */
    public function isPermuted()
    {
        return $this->permuted;
    }
}
