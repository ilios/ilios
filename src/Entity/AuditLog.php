<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\AuditLogRepository;

#[ORM\Table(name: 'audit_log')]
#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog implements AuditLogInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 16)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 16)]
    protected string $action;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    protected DateTime $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    protected string $objectId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255)]
    protected string $objectClass;

    #[ORM\Column(type: 'text', length: 1000)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 1000)]
    protected string $valuesChanged;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'auditLogs')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    protected UserInterface $user;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function setAction(string $action)
    {
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * object ID sometimes comes as an int,
     * so we need to cast it back to a string for storage
     */
    public function setObjectId(mixed $objectId)
    {
        $this->objectId = (string) $objectId;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function setObjectClass(string $objectClass)
    {
        $this->objectClass = $objectClass;
    }

    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    public function setValuesChanged(string $valuesChanged)
    {
        $this->valuesChanged = $valuesChanged;
    }

    public function getValuesChanged(): string
    {
        return $this->valuesChanged;
    }

    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
