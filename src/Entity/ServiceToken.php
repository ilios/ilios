<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\ServiceTokenRepository;
use App\Traits\AlertableEntity;
use App\Traits\CreatedAtEntity;
use App\Traits\DescribableEntity;
use App\Traits\EnableableEntity;
use App\Traits\IdentifiableEntity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'service_token')]
#[ORM\Entity(repositoryClass: ServiceTokenRepository::class)]
class ServiceToken implements ServiceTokenInterface
{
    use AlertableEntity;
    use CreatedAtEntity;
    use DescribableEntity;
    use EnableableEntity;
    use IdentifiableEntity;

    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[IA\Type('boolean')]
    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false)]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $enabled;

    #[IA\Type('string')]
    #[ORM\Column(name: 'description', type: 'text', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[IA\Type('dateTime')]
    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    protected DateTime $createdAt;

    #[IA\Type('dateTime')]
    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    protected DateTime $expiresAt;

    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    #[ORM\OneToMany(mappedBy: 'serviceToken', targetEntity: 'AuditLog')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $auditLogs;

    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'serviceTokenInstigators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $alerts;

    public function __construct()
    {
        $this->enabled = true;
        $this->auditLogs = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

    public function setAuditLogs(Collection $auditLogs): void
    {
        $this->auditLogs = new ArrayCollection();

        foreach ($auditLogs as $auditLog) {
            $this->addAuditLog($auditLog);
        }
    }

    public function addAuditLog(AuditLogInterface $auditLog): void
    {
        if (!$this->auditLogs->contains($auditLog)) {
            $this->auditLogs->add($auditLog);
        }
    }

    public function removeAuditLog(AuditLogInterface $auditLog): void
    {
        $this->auditLogs->removeElement($auditLog);
    }

    public function getAuditLogs(): Collection
    {
        return $this->auditLogs;
    }

    public function setExpiresAt(DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function addAlert(AlertInterface $alert): void
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addServiceTokenInstigator($this);
        }
    }

    public function removeAlert(AlertInterface $alert): void
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeServiceTokenInstigator($this);
        }
    }
}
