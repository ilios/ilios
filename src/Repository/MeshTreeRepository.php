<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\MeshTree;
use App\Entity\DTO\MeshTreeDTO;
use Doctrine\Persistence\ManagerRegistry;

class MeshTreeRepository extends ServiceEntityRepository implements DTORepositoryInterface, ManagerInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeshTree::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\MeshTree', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('App\Entity\MeshTree', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var MeshTreeDTO[] $meshTreeDTOs */
        $meshTreeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $meshTreeDTOs[$arr['id']] = new MeshTreeDTO(
                $arr['id'],
                $arr['treeNumber']
            );
        }
        $meshTreeIds = array_keys($meshTreeDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, descriptor.id AS descriptorId'
            )
            ->from('App\Entity\MeshTree', 'x')
            ->join('x.descriptor', 'descriptor')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $meshTreeIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $meshTreeDTOs[$arr['xId']]->descriptor = (string) $arr['descriptorId'];
        }

        return array_values($meshTreeDTOs);
    }


    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (count($criteria)) {
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
