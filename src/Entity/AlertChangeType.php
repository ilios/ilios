<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\AlertableEntity;
use App\Attribute as IA;
use App\Repository\AlertChangeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\AlertInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;

#[ORM\Table(name: 'alert_change_type')]
#[ORM\Entity(repositoryClass: AlertChangeTypeRepository::class)]
#[IA\Entity]
class AlertChangeType implements AlertChangeTypeInterface
{
    use TitledEntity;
    use StringableIdEntity;
    use IdentifiableEntity;
    use AlertableEntity;

    #[ORM\Column(name: 'alert_change_type_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'changeTypes')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected Collection $alerts;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
    }

    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addChangeType($this);
        }
    }

    public function removeAlert(AlertInterface $alert)
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeChangeType($this);
        }
    }
}
