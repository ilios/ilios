<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\VocabularyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'vocabulary')]
#[ORM\UniqueConstraint(name: 'unique_vocabulary_title', columns: ['school_id', 'title'])]
#[ORM\Entity(repositoryClass: VocabularyRepository::class)]
#[IA\Entity]
class Vocabulary implements VocabularyInterface
{
    #[ORM\Column(name: 'vocabulary_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'vocabularies')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\OneToMany(mappedBy: 'vocabulary', targetEntity: Term::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
        $this->active = true;
    }

    #[Override]
    public function getIndexableCourses(): array
    {
        $termCourses = $this->terms->map(fn(TermInterface $term) => $term->getIndexableCourses());

        return count($termCourses) ? array_merge(...$termCourses->toArray()) : [];
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function isActive(): bool
    {
        return $this->active;
    }

    #[Override]
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
    #[Override]
    public function setTerms(?Collection $terms = null): void
    {
        $this->terms = new ArrayCollection();
        if (is_null($terms)) {
            return;
        }

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    #[Override]
    public function addTerm(TermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }

    #[Override]
    public function removeTerm(TermInterface $term): void
    {
        $this->terms->removeElement($term);
    }

    #[Override]
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->school;
    }

    #[Override]
    public function setSchool(SchoolInterface $school): void
    {
        $this->school = $school;
    }

    #[Override]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    #[Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
