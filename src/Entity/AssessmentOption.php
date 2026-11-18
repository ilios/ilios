<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Attributes as IA;
use App\Repository\AssessmentOptionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Override;

#[ORM\Table(name: 'assessment_option')]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[ORM\Entity(repositoryClass: AssessmentOptionRepository::class)]
#[IA\Entity]
class AssessmentOption implements AssessmentOptionInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'assessment_option_id', type: 'integer', length: 10)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 20)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 18)]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'assessmentOption', targetEntity: SessionType::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionTypes;

    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function setSessionTypes(Collection $sessionTypes): void
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    #[Override]
    public function addSessionType(SessionTypeInterface $sessionType): void
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
        }
    }

    #[Override]
    public function removeSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionTypes->removeElement($sessionType);
    }

    #[Override]
    public function getSessionTypes(): Collection
    {
        return $this->sessionTypes;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
