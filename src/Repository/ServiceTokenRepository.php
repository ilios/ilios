<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServiceToken;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class ServiceTokenRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ServiceToken::class);
    }

    /**
     * @throws Exception
     */
    public function findDTOBy(array $criteria): ?object
    {
        throw new Exception("not implemented");
    }

    /**
     * @throws Exception
     */
    public function findDTOsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        throw new Exception("not implemented");
    }

    /**
     * @throws Exception
     */
    protected function hydrateDTOsFromIds(array $ids): array
    {
        throw new Exception("not implemented");
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
