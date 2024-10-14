<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\PendingUserUpdate;
use App\Entity\DTO\PendingUserUpdateDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;

class PendingUserUpdateRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, PendingUserUpdate::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(PendingUserUpdate::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new PendingUserUpdateDTO(
                $arr['id'],
                $arr['type'],
                $arr['property'],
                $arr['value']
            );
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId'
            )
            ->from(PendingUserUpdate::class, 'x')
            ->join('x.user', 'user')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->user = (int) $arr['userId'];
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
        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.user', 's_user');
            $qb->join('s_user.school', 'school');
            $qb->andWhere($qb->expr()->in('school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
            unset($criteria['schools']);
        }


        if (array_key_exists('users', $criteria)) {
            $ids = is_array($criteria['users']) ? $criteria['users'] : [$criteria['users']];
            $qb->join('x.user', 'user');
            $qb->andWhere($qb->expr()->in('user.id', ':users'));
            $qb->setParameter(':users', $ids);
            unset($criteria['users']);
        }

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * Remove all pending user updates from the database
     */
    public function removeAllPendingUserUpdates()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(PendingUserUpdate::class, 'p');
        $qb->getQuery()->execute();
    }
}
