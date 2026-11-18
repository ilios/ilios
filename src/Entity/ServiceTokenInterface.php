<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\AlertableEntityInterface;
use App\Entity\CreatedAtEntityInterface;
use App\Entity\DescribableEntityInterface;
use App\Entity\EnableableEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;

interface ServiceTokenInterface extends
    AlertableEntityInterface,
    CreatedAtEntityInterface,
    DescribableEntityInterface,
    EnableableEntityInterface,
    IdentifiableEntityInterface
{
    public function setAuditLogs(Collection $auditLogs): void;
    public function addAuditLog(AuditLogInterface $auditLog): void;
    public function removeAuditLog(AuditLogInterface $auditLog): void;
    public function getAuditLogs(): Collection;
    public function setExpiresAt(DateTime $expiresAt): void;
    public function getExpiresAt(): DateTime;
}
