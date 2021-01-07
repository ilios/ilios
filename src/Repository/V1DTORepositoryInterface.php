<?php

declare(strict_types=1);

namespace App\Repository;

interface V1DTORepositoryInterface
{
    /**
     * Find a single DTO
     */
    public function findV1DTOBy(array $criteria);

    /**
     * Find and hydrate as V1 DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findV1DTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
}
