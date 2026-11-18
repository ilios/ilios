<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SchoolConfigRepository;
use Override;

#[ORM\Table(name: 'school_config')]
#[ORM\UniqueConstraint(name: 'school_conf_uniq', columns: ['school_id', 'name'])]
#[ORM\Entity(repositoryClass: SchoolConfigRepository::class)]
#[IA\Entity]
class SchoolConfig implements SchoolConfigInterface
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
    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 65000)]
    protected string $value;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'configurations')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

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
}
