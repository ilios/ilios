<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\ObjectiveDTO;

class ObjectiveRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT o')->from('IliosCoreBundle:Objective', 'o');

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
        $qb = $this->_em->createQueryBuilder()->select('o')->distinct()->from('IliosCoreBundle:Objective', 'o');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $objectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $objectiveDTOs[$arr['id']] = new ObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position']
            );
        }
        $objectiveIds = array_keys($objectiveDTOs);
        $qb = $this->_em->createQueryBuilder()
            ->select('o.id as objectiveId, c.id as competencyId, a.id as ancestorId')
            ->from('IliosCoreBundle:Objective', 'o')
            ->leftJoin('o.competency', 'c')
            ->leftJoin('o.ancestor', 'a')
            ->where($qb->expr()->in('o.id', ':ids'))
            ->setParameter('ids', $objectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $objectiveDTOs[$arr['objectiveId']]->competency = $arr['competencyId']?(int)$arr['competencyId']:null;
            $objectiveDTOs[$arr['objectiveId']]->ancestor = $arr['ancestorId']?(int)$arr['ancestorId']:null;
        }
        $related = [
            'courses',
            'programYears',
            'sessions',
            'parents',
            'children',
            'meshDescriptors',
            'descendants'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, o.id AS objectiveId')->from('IliosCoreBundle:Objective', 'o')
                ->join("o.{$rel}", 'r')
                ->where($qb->expr()->in('o.id', ':objectiveIds'))
                ->orderBy('relId')
                ->setParameter('objectiveIds', $objectiveIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $objectiveDTOs[$arr['objectiveId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($objectiveDTOs);
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
            $qb->join('o.courses', 'course');
            $qb->andWhere($qb->expr()->in('course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }
        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('o.programYears', 'programYear');
            $qb->andWhere($qb->expr()->in('programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('o.sessions', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['programYears']);
        unset($criteria['sessions']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("o.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('o.'.$sort, $order);
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
