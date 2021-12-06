<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;

/**
 * Interface IngestionExceptionInterface
 */
interface IngestionExceptionInterface extends IdentifiableEntityInterface
{
    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;

    /**
     * @param string $uid
     */
    public function setUid($uid);

    public function getUid(): string;
}
