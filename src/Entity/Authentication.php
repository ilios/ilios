<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\AuthenticationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Override;

#[ORM\Table(name: 'authentication')]
#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
#[IA\Entity]
class Authentication implements AuthenticationInterface
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'authentication', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[IA\Type('entity')]
    #[IA\Expose]
    #[Assert\NotBlank]
    protected UserInterface $user;

    #[ORM\Column(name: 'username', type: 'string', length: 100, unique: true, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 100)]
    private ?string $username = null;

    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    private ?string $passwordHash = null;

    #[ORM\Column(name: 'invalidate_token_issued_before', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\Type(DateTimeInterface::class)]
    protected ?DateTime $invalidateTokenIssuedBefore = null;

    #[Override]
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    #[Override]
    public function getUsername(): ?string
    {
        return $this->username;
    }

    #[Override]
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    #[Override]
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    #[Override]
    public function getPassword(): ?string
    {
        return $this->getPasswordHash();
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
    public function setInvalidateTokenIssuedBefore(?DateTime $invalidateTokenIssuedBefore = null): void
    {
        $this->invalidateTokenIssuedBefore = $invalidateTokenIssuedBefore;
    }

    #[Override]
    public function getInvalidateTokenIssuedBefore(): ?DateTime
    {
        return $this->invalidateTokenIssuedBefore;
    }

    public function __toString(): string
    {
        return (string) $this->user;
    }
}
