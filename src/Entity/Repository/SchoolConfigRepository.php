<?php
namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\SchoolConfigDTO;

/**
 * Class SchoolConfigRepository
 */
class SchoolConfigRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\SchoolConfig', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('App\Entity\SchoolConfig', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $schoolConfigDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $schoolConfigDTOs[$arr['id']] = new SchoolConfigDTO(
                $arr['id'],
                $arr['name'],
                $arr['value']
            );
        }
        $schoolConfigIds = array_keys($schoolConfigDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as xId, school.id AS schoolId')
            ->from('App\Entity\SchoolConfig', 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $schoolConfigIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $schoolConfigDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        return array_values($schoolConfigDTOs);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getValue($name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('x.value')->from('App\Entity\SchoolConfig', 'x')
            ->where($qb->expr()->eq('x.name', ':name'))
            ->setParameter('name', $name);

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = null;
        }

        return $result;
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
