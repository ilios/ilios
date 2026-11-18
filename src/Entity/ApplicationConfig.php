<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use App\Repository\ApplicationConfigRepository;
use App\Traits\StringableIdEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Override;

#[ORM\Table(name: 'application_config')]
#[ORM\UniqueConstraint(name: 'app_conf_uniq', columns: ['name'])]
#[ORM\Entity(repositoryClass: ApplicationConfigRepository::class)]
#[IA\Entity]
class ApplicationConfig implements ApplicationConfigInterface
{
    use StringableIdEntity;

    #[ORM\Column(name: 'id', type: 'integer')]
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
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 200)]
    protected string $name;

    #[ORM\Column(name: 'value', type: 'text', nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $value;

    #[Override]
    public function getValue(): string
    {
        return $this->value;
    }

    #[Override]
    public function setValue(string $value): void
    {
        $this->value = $value;
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
}
