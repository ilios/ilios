<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\UserInterface;

/**
 * interface BlameableEntityInterface
 */
interface BlameableEntityInterface
{
    public function setCreatedBy(UserInterface $createdBy);

    /**
     * Returns createdBy.
     */
    public function getCreatedBy(): UserInterface;

    public function setUpdatedBy(UserInterface $updatedBy);

    public function getUpdatedBy(): UserInterface;
}
