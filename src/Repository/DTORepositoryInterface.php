<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

interface DTORepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}
