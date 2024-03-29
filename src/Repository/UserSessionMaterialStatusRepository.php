<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DTO\UserSessionMaterialStatusDTO;
use App\Entity\UserSessionMaterialStatus;
use App\Service\DTOCacheManager;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;

class UserSessionMaterialStatusRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, UserSessionMaterialStatus::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('x')->distinct()
            ->from(UserSessionMaterialStatus::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new UserSessionMaterialStatusDTO(
                (int) $arr['id'], //doctrine stores bigint as a string, we need to cast it back
                $arr['status'],
                $arr['updatedAt'],
            );
        }

        return $this->attachAssociationsToDTOs($dtos);
    }

    protected function attachAssociationsToDTOs(array $dtos): array
    {
        if ($dtos === []) {
            return $dtos;
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('x.id AS xId, u.id AS userId, m.id AS materialId')
            ->from(UserSessionMaterialStatus::class, 'x')
            ->join('x.user', 'u')
            ->join('x.material', 'm')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->user = $arr['userId'];
            $dtos[$arr['xId']]->material = $arr['materialId'];
        }

        return array_values($dtos);
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('statuses', $criteria)) {
            $ids = $criteria['statuses'];
            $qb->andWhere($qb->expr()->in('x.status', ':statuses'));
            $qb->setParameter(':statuses', $ids);
        }
        if (array_key_exists('materials', $criteria)) {
            $ids = $criteria['materials'];
            $qb->join('x.material', 'material');
            $qb->andWhere($qb->expr()->in('material.id', ':materials'));
            $qb->setParameter(':materials', $ids);
        }
        if (array_key_exists('users', $criteria)) {
            $ids = $criteria['users'];
            $qb->join('x.user', 'user');
            $qb->andWhere($qb->expr()->in('user.id', ':users'));
            $qb->setParameter(':users', $ids);
        }

        unset($criteria['statuses']);
        unset($criteria['materials']);
        unset($criteria['users']);
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
