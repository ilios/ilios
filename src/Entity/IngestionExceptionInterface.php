<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use Stringable;

interface IngestionExceptionInterface extends IdentifiableEntityInterface, Stringable
{
    public function setUser(UserInterface $user): void;
    public function getUser(): UserInterface;

    public function setUid(string $uid): void;
    public function getUid(): string;
}
