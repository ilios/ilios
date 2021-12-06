<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\AuditLogRepository;

/**
 * Class AuditLog
 */
#[ORM\Table(name: 'audit_log')]
#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog implements AuditLogInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16
     * )
     */
    #[ORM\Column(type: 'string', length: 16)]
    protected $action;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'datetime')]
    protected $createdAt;

    /**
     * @var int
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected $objectId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     */
    #[ORM\Column(type: 'string', length: 255)]
    protected $objectClass;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 1000
     * )
     */
    #[ORM\Column(type: 'text', length: 1000)]
    protected $valuesChanged;

    /**
     * @var UserInterface
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'auditLogs')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    protected $user;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Set action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get createdAt
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Set objectId
     *
     * @param string $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * Get objectId
     */
    public function getObjectId(): string
    {
        return $this->objectId;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    }

    /**
     * Get objectClass
     */
    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    /**
     * Set valuesChanged
     *
     * @param string $valuesChanged
     */
    public function setValuesChanged($valuesChanged)
    {
        $this->valuesChanged = $valuesChanged;
    }

    /**
     * Get valuesChanged
     */
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
