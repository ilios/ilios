<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SessionTypesEntity;
use App\Attributes as IA;
use App\Repository\AamcMethodRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ActivatableEntity;

#[ORM\Table(name: 'aamc_method')]
#[ORM\Entity(repositoryClass: AamcMethodRepository::class)]
#[IA\Entity]
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableStringEntity;
    use SessionTypesEntity;
    use ActivatableEntity;

    #[ORM\Column(name: 'method_id', type: 'string', length: 10)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected string $id;

    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: 'SessionType', mappedBy: 'aamcMethods')]
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

    public function addSessionType(SessionTypeInterface $sessionType): void
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
            $sessionType->addAamcMethod($this);
        }
    }

    public function removeSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionTypes->removeElement($sessionType);
        $sessionType->removeAamcMethod($this);
    }

    public function setDescription(string $description): void
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
