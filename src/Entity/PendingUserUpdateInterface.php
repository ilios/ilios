<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface PendingUserUpdateInterface extends
    IdentifiableEntityInterface,
    Stringable
{
    public function setType(string $type);
    public function getType(): string;

    public function setProperty(?string $property);
    public function getProperty(): ?string;

    public function setValue(?string $value);
    public function getValue(): ?string;

    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;
}
