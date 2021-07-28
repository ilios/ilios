<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AamcMethod;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AamcMethodDTO;
use Doctrine\Persistence\ManagerRegistry;

class AamcMethodRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AamcMethod::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\AamcMethod', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('App\Entity\AamcMethod', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $aamcMethodDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $aamcMethodDTOs[$arr['id']] = new AamcMethodDTO(
                $arr['id'],
                $arr['description'],
                $arr['active']
            );
        }
        $aamcMethodIds = array_keys($aamcMethodDTOs);
        $related = [
            'sessionTypes'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS aamcMethodId')->from('App\Entity\AamcMethod', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':aamcMethodIds'))
                ->orderBy('relId')
                ->setParameter('aamcMethodIds', $aamcMethodIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $aamcMethodDTOs[$arr['aamcMethodId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($aamcMethodDTOs);
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
        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->join('x.sessionTypes', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sessionTypes']);

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

    public function import(array $data, string $type = null, string $now = null): void
    {
        // `method_id`,`description`,`active`
        $entity = new AamcMethod();
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        $entity->setActive((bool) $data[2]);
        $this->update($entity, true);
    }

    /**
     * Delete all records in this table
     */
    public function deleteAll(): void
    {
        $this->createQueryBuilder('a')->delete()->getQuery()->execute();
    }
}
