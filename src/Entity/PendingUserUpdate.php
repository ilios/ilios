<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\PendingUserUpdateRepository;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'pending_user_update')]
#[ORM\Entity(repositoryClass: PendingUserUpdateRepository::class)]
#[IA\Entity]
class PendingUserUpdate implements PendingUserUpdateInterface
{
    #[ORM\Column(name: 'exception_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 32)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 32)]
    protected string $type;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 32)]
    protected ?string $property = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    protected ?string $value = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pendingUserUpdates')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected UserInterface $user;

    #[Override]
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    public function setProperty(?string $property): void
    {
        $this->property = $property;
    }

    #[Override]
    public function getProperty(): ?string
    {
        return $this->property;
    }

    #[Override]
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    #[Override]
    public function getValue(): ?string
    {
        return $this->value;
    }

    #[Override]
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    #[Override]
    public function getUser(): UserInterface
    {
        return $this->user;
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
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
