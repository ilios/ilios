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
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;

    /**
     * Searches the data store for a single object by given criteria and sort order.
     */
    public function findDTOBy(array $criteria): ?object;
}
