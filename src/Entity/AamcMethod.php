<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use App\Repository\AamcMethodRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Override;

#[ORM\Table(name: 'aamc_method')]
#[ORM\Entity(repositoryClass: AamcMethodRepository::class)]
#[IA\Entity]
class AamcMethod implements AamcMethodInterface
{
    #[ORM\Column(name: 'method_id', type: 'string', length: 10)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected ?string $id;

    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: SessionType::class, mappedBy: 'aamcMethods')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionTypes;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
        $this->active = true;
    }

    #[Override]
    public function addSessionType(SessionTypeInterface $sessionType): void
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
            $sessionType->addAamcMethod($this);
        }
    }

    #[Override]
    public function removeSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionTypes->removeElement($sessionType);
        $sessionType->removeAamcMethod($this);
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
    public function getSessionTypes(): Collection
    {
        return $this->sessionTypes;
    }

    #[Override]
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    #[Override]
    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
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
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?string
    {
        return $this->id;
    }
}
