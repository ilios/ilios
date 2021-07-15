<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use App\Classes\SessionUser;
use App\Classes\SessionUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use DateTimeInterface;

/**
 * Class Authentication
 */
#[ORM\Table(name: 'authentication')]
#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
#[IA\Entity]
class Authentication implements AuthenticationInterface
{
    /**
     * @var UserInterface
     * @Assert\NotBlank()
     */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'authentication', targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[IA\Type('entity')]
    #[IA\Expose]
    protected $user;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=100)
     * })
     */
    #[ORM\Column(name: 'username', type: 'string', length: 100, unique: true, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    private $username;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     */
    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true)]
    private $passwordHash;

    /**
     * @Assert\Type(DateTimeInterface::class)
     */
    #[ORM\Column(name: 'invalidate_token_issued_before', type: 'datetime', nullable: true)]
    #[IA\Expose]
    #[IA\ReadOnly]
    #[IA\Type('dateTime')]
    protected $invalidateTokenIssuedBefore;

    /**
     * @inheritdoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->getPasswordHash();
    }

    /**
     * @inheritdoc
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function setInvalidateTokenIssuedBefore(\DateTime $invalidateTokenIssuedBefore = null)
    {
        $this->invalidateTokenIssuedBefore = $invalidateTokenIssuedBefore;
    }

    /**
     * @inheritdoc
     */
    public function getInvalidateTokenIssuedBefore()
    {
        return $this->invalidateTokenIssuedBefore;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) $this->user;
    }
}
