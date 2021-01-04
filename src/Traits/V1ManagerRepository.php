<?php

declare(strict_types=1);

namespace App\Traits;

trait V1ManagerRepository
{
    abstract public function findV1DTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;

    public function findV1DTOBy(array $criteria)
    {
        $results = $this->findV1DTOsBy($criteria, null, 1);
        return empty($results) ? false : $results[0];
    }
}
