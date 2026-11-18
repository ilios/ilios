<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\AlertChangeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'alert_change_type')]
#[ORM\Entity(repositoryClass: AlertChangeTypeRepository::class)]
#[IA\Entity]
class AlertChangeType implements AlertChangeTypeInterface
{
    #[ORM\Column(name: 'alert_change_type_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\ManyToMany(targetEntity: Alert::class, mappedBy: 'changeTypes')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $alerts;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
    }

    #[Override]
    public function addAlert(AlertInterface $alert): void
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addChangeType($this);
        }
    }

    #[Override]
    public function removeAlert(AlertInterface $alert): void
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeChangeType($this);
        }
    }

    #[Override]
    public function setAlerts(?Collection $alerts = null): void
    {
        $this->alerts = new ArrayCollection();
        if (is_null($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    #[Override]
    public function getAlerts(): Collection
    {
        return $this->alerts;
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
