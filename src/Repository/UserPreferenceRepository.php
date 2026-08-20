<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserPreference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserPreferenceRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, UserPreference::class);
    }

    public function getJsonForUser(int $id): ?string
    {
        $qb = $this->createQueryBuilder('x')
            ->select('x.json')
            ->where('IDENTITY(x.user) = :id')
            ->setParameter('id', $id);

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['json'] ?? null;
    }
}
