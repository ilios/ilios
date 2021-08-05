<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\MeshPreviousIndexing;
use App\Entity\DTO\MeshPreviousIndexingDTO;
use Doctrine\Persistence\ManagerRegistry;

class MeshPreviousIndexingRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeshPreviousIndexing::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\MeshPreviousIndexing', 'x');

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
            ->distinct()->from('App\Entity\MeshPreviousIndexing', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var MeshPreviousIndexingDTO[] $meshPreviousIndexingDTOs */
        $meshPreviousIndexingDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $meshPreviousIndexingDTOs[$arr['id']] = new MeshPreviousIndexingDTO(
                $arr['id'],
                $arr['previousIndexing']
            );
        }
        $meshPreviousIndexingIds = array_keys($meshPreviousIndexingDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, descriptor.id AS descriptorId'
            )
            ->from('App\Entity\MeshPreviousIndexing', 'x')
            ->join('x.descriptor', 'descriptor')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $meshPreviousIndexingIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $meshPreviousIndexingDTOs[$arr['xId']]->descriptor = $arr['descriptorId'];
        }

        return array_values($meshPreviousIndexingDTOs);
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('x_session.course', 'x_course');
            $qb->andWhere($qb->expr()->in('x_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
            unset($criteria['courses']);
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
}
