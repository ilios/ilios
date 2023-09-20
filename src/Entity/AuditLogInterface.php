<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface AuditLogInterface extends
    CreatedAtEntityInterface,
    IdentifiableEntityInterface,
    Stringable
{
    public function setAction(string $action): void;
    public function getAction(): string;

    public function setObjectId(mixed $objectId): void;
    public function getObjectId(): string;

    public function setObjectClass(string $objectClass): void;
    public function getObjectClass(): string;

    public function setUser(?UserInterface $user): void;
    public function getUser(): ?UserInterface;

    public function setServiceToken(?ServiceTokenInterface $serviceToken): void;
    public function getServiceToken(): ?ServiceTokenInterface;
}
