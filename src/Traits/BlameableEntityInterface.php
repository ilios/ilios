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
     *
     * @return UserInterface
     */
    public function getCreatedBy();

    public function setUpdatedBy(UserInterface $updatedBy);

    /**
     * @return UserInterface
     */
    public function getUpdatedBy();
}
