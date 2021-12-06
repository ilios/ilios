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

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;

    /**
     * @param string $uid
     */
    public function setUid($uid);

    /**
     * @return string
     */
    public function getUid(): string;
}
