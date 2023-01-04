<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use Stringable;

interface IngestionExceptionInterface extends IdentifiableEntityInterface, Stringable
{
    public function setUser(UserInterface $user);
    public function getUser(): UserInterface;

    public function setUid(string $uid);
    public function getUid(): string;
}
