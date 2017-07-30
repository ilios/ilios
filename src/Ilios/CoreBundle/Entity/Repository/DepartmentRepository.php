<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\DepartmentDTO;

/**
 * Class DepartmentRepository
 */
class DepartmentRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:Department', 'x');

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
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('IliosCoreBundle:Department', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $departmentDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $departmentDTOs[$arr['id']] = new DepartmentDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $departmentIds = array_keys($departmentDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as departmentId, s.id as schoolId')
            ->from('IliosCoreBundle:Department', 'x')
            ->join('x.school', 's')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $departmentIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $departmentDTOs[$arr['departmentId']]->school = (int) $arr['schoolId'];
        }

        $related = [
            'stewards',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS departmentId')->from('IliosCoreBundle:Department', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':departmentIds'))
                ->orderBy('relId')
                ->setParameter('departmentIds', $departmentIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $departmentDTOs[$arr['departmentId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($departmentDTOs);
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
        if (array_key_exists('stewards', $criteria)) {
            $ids = is_array($criteria['stewards']) ? $criteria['stewards'] : [$criteria['stewards']];
            $qb->join('x.stewards', 'x_stewards');
            $qb->andWhere($qb->expr()->in('x_stewards.id', ':stewards'));
            $qb->setParameter(':stewards', $ids);
        }
        unset($criteria['stewards']);

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
                $qb->addOrderBy('x.'.$sort, $order);
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
