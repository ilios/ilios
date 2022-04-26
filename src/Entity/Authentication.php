<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribute as IA;
use App\Repository\AuthenticationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Authentication
 */
#[ORM\Table(name: 'authentication')]
#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
#[IA\Entity]
class Authentication implements AuthenticationInterface, Stringable
{
    /**
     * @var UserInterface
     */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'authentication', targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[IA\Type('entity')]
    #[IA\Expose]
    #[Assert\NotBlank]
    protected $user;

    /**
     * @var string
     */
    #[ORM\Column(name: 'username', type: 'string', length: 100, unique: true, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    private $username;

    /**
     * @var string
     */
    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    private $passwordHash;

    #[ORM\Column(name: 'invalidate_token_issued_before', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\Type(DateTimeInterface::class)]
    protected $invalidateTokenIssuedBefore;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getPassword(): ?string
    {
        return $this->getPasswordHash();
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setInvalidateTokenIssuedBefore(DateTime $invalidateTokenIssuedBefore = null)
    {
        $this->invalidateTokenIssuedBefore = $invalidateTokenIssuedBefore;
    }

    public function getInvalidateTokenIssuedBefore(): ?DateTime
    {
        return $this->invalidateTokenIssuedBefore;
    }

    public function __toString(): string
    {
        return (string) $this->user;
    }
}
