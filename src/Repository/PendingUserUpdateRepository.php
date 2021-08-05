<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\PendingUserUpdate;
use App\Entity\DTO\PendingUserUpdateDTO;
use Doctrine\Persistence\ManagerRegistry;

class PendingUserUpdateRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingUserUpdate::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\PendingUserUpdate', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('App\Entity\PendingUserUpdate', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var PendingUserUpdateDTO[] $pendingUserUpdateDTOs */
        $pendingUserUpdateDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $pendingUserUpdateDTOs[$arr['id']] = new PendingUserUpdateDTO(
                $arr['id'],
                $arr['type'],
                $arr['property'],
                $arr['value']
            );
        }
        $pendingUserUpdateIds = array_keys($pendingUserUpdateDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId'
            )
            ->from('App\Entity\PendingUserUpdate', 'x')
            ->join('x.user', 'user')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $pendingUserUpdateIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $pendingUserUpdateDTOs[$arr['xId']]->user = (int) $arr['userId'];
        }

        return array_values($pendingUserUpdateDTOs);
    }


    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
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

        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * Remove all pending user updates from the database
     */
    public function removeAllPendingUserUpdates()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete('App\Entity\PendingUserUpdate', 'p');
        $qb->getQuery()->execute();
    }
}
