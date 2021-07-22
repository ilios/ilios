<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AlertChangeType;
use App\Traits\ClearableRepository;
use App\Traits\ClearableRepositoryInterface;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AlertChangeTypeDTO;
use Doctrine\Persistence\ManagerRegistry;

class AlertChangeTypeRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface,
    ClearableRepositoryInterface
{
    use ManagerRepository;
    use ClearableRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertChangeType::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\AlertChangeType', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('App\Entity\AlertChangeType', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $alertChangeTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $alertChangeTypeDTOs[$arr['id']] = new AlertChangeTypeDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $alertChangeTypeIds = array_keys($alertChangeTypeDTOs);
        $related = [
            'alerts'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS alertChangeTypeId')->from('App\Entity\AlertChangeType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':alertChangeTypeIds'))
                ->orderBy('relId')
                ->setParameter('alertChangeTypeIds', $alertChangeTypeIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $alertChangeTypeDTOs[$arr['alertChangeTypeId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($alertChangeTypeDTOs);
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
        if (array_key_exists('alerts', $criteria)) {
            $ids = is_array($criteria['alerts']) ? $criteria['alerts'] : [$criteria['alerts']];
            $qb->join('x.alerts', 'al');
            $qb->andWhere($qb->expr()->in('al.id', ':alerts'));
            $qb->setParameter(':alerts', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['alerts']);

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
     * @inheritdoc
     */
    public function import(array $data, string $type = null, string $now = null): void
    {
        $sql = "INSERT INTO alert_change_type (alert_change_type_id, title) VALUES (?, ?)";
        $connection = $this->_em->getConnection();
        $connection->executeStatement($sql, $data);
    }
}
