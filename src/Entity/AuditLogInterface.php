<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use DateTime;
use Stringable;

interface AuditLogInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setAction(string $action);
    public function getAction(): string;

    public function getCreatedAt(): DateTime;
    public function setCreatedAt(DateTime $createdAt);

    public function setObjectId(mixed $objectId);
    public function getObjectId(): string;

    public function setObjectClass(string $objectClass);
    public function getObjectClass(): string;

    public function setUser(UserInterface $user);
    public function getUser(): UserInterface;
}
