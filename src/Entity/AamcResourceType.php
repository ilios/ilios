<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CategorizableEntity;
use App\Traits\TitledEntity;
use App\Attributes as IA;
use App\Repository\AamcResourceTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AamcResourceTypeRepository::class)]
#[ORM\Table(name: 'aamc_resource_type')]
#[IA\Entity]
class AamcResourceType implements AamcResourceTypeInterface
{
    use IdentifiableStringEntity;
    use TitledEntity;
    use CategorizableEntity;

    #[ORM\Column(name: 'resource_type_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 21)]
    protected string $id;

    #[ORM\Column(type: 'string', length: 200)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected string $title;

    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: 'Term', mappedBy: 'aamcResourceTypes')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    public function addTerm(TermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addAamcResourceType($this);
        }
    }

    public function removeTerm(TermInterface $term): void
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeAamcResourceType($this);
        }
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
