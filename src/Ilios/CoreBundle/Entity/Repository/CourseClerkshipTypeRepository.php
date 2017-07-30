<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CourseClerkshipTypeDTO;

/**
 * Class CourseClerkshipTypeRepository
 */
class CourseClerkshipTypeRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:CourseClerkshipType', 'x');

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
        $qb = $this->_em->createQueryBuilder()
            ->select('x')->distinct()
            ->from('IliosCoreBundle:CourseClerkshipType', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $courseClerkshipTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseClerkshipTypeDTOs[$arr['id']] = new CourseClerkshipTypeDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $courseClerkshipTypeIds = array_keys($courseClerkshipTypeDTOs);
        $related = [
            'courses'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS courseClerkshipTypeId')
                ->from('IliosCoreBundle:CourseClerkshipType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':courseClerkshipTypeIds'))
                ->orderBy('relId')
                ->setParameter('courseClerkshipTypeIds', $courseClerkshipTypeIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $courseClerkshipTypeDTOs[$arr['courseClerkshipTypeId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($courseClerkshipTypeDTOs);
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.courses', 'c');
            $qb->andWhere($qb->expr()->in('c.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);

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
