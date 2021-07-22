<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AamcResourceType;
use App\Traits\ClearableRepository;
use App\Traits\ClearableRepositoryInterface;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AamcResourceTypeDTO;
use Doctrine\Persistence\ManagerRegistry;

class AamcResourceTypeRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface,
    ClearableRepositoryInterface
{
    use ManagerRepository;
    use ClearableRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AamcResourceType::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\AamcResourceType', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('App\Entity\AamcResourceType', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $aamcResourceTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $aamcResourceTypeDTOs[$arr['id']] = new AamcResourceTypeDTO(
                $arr['id'],
                $arr['title'],
                $arr['description']
            );
        }
        $aamcResourceTypeIds = array_keys($aamcResourceTypeDTOs);
        $related = [
            'terms'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS aamcResourceTypeId')->from('App\Entity\AamcResourceType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':aamcResourceTypeIds'))
                ->orderBy('relId')
                ->setParameter('aamcResourceTypeIds', $aamcResourceTypeIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $aamcResourceTypeDTOs[$arr['aamcResourceTypeId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($aamcResourceTypeDTOs);
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
        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('x.terms', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['terms']);

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
        $sql = "INSERT INTO aamc_resource_type (resource_type_id, title, description) VALUES (?, ?, ?)";
        $connection = $this->_em->getConnection();
        $connection->executeStatement($sql, $data);
    }
}
