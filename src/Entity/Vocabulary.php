<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\CategorizableEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\SchoolEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Repository\VocabularyRepository;

/**
 * Class Vocabulary
 * @IS\Entity
 */
#[ORM\Table(name: 'vocabulary')]
#[ORM\UniqueConstraint(name: 'unique_vocabulary_title', columns: ['school_id', 'title'])]
#[ORM\Entity(repositoryClass: VocabularyRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'vocabulary_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    protected $title;

    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'vocabularies')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    protected $school;

    /**
     * @var ArrayCollection|TermInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'vocabulary', targetEntity: 'Term')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $terms;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
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
