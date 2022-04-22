<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\CategorizableEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\SchoolEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Repository\VocabularyRepository;

/**
 * Class Vocabulary
 */
#[ORM\Table(name: 'vocabulary')]
#[ORM\UniqueConstraint(name: 'unique_vocabulary_title', columns: ['school_id', 'title'])]
#[ORM\Entity(repositoryClass: VocabularyRepository::class)]
#[IA\Entity]
class Vocabulary implements VocabularyInterface
{
    use IdentifiableEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use TitledEntity;
    use CategorizableEntity;
    use ActivatableEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'vocabulary_id', type: 'integer')]
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
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected $title;

    /**
     * @var SchoolInterface
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'vocabularies')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $school;

    /**
     * @var ArrayCollection|TermInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'vocabulary', targetEntity: 'Term')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected $active;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        $termCourses = $this->terms->map(fn(TermInterface $term) => $term->getIndexableCourses());

        return count($termCourses) ? array_merge(...$termCourses) : [];
    }
}
