<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface PendingUserUpdateInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setType(string $type): void;
    public function getType(): string;

    public function setProperty(?string $property): void;
    public function getProperty(): ?string;

    public function setValue(?string $value): void;
    public function getValue(): ?string;

    public function setUser(UserInterface $user): void;

    public function getUser(): UserInterface;
}
