<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use App\Classes\SessionUser;
use App\Classes\SessionUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use DateTimeInterface;

/**
 * Class Authentication
 * @IS\Entity
 */
#[ORM\Table(name: 'authentication')]
#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
class Authentication implements AuthenticationInterface
{
    /**
     * @var UserInterface
     * })
     * @Assert\NotBlank()
     * @IS\Type("entity")
     * @IS\Expose
     */
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: 'User', inversedBy: 'authentication')]
    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    protected $user;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=100)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'username', type: 'string', unique: true, length: 100, nullable: true)]
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
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'invalidate_token_issued_before', type: 'datetime', nullable: true)]
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
