<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CohortDTO;

/**
 * Class CohortRepository
 */
class CohortRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT c')->from('IliosCoreBundle:Cohort', 'c');

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
        $qb = $this->_em->createQueryBuilder()->select('c')->distinct()->from('IliosCoreBundle:Cohort', 'c');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $cohortDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $cohortDTOs[$arr['id']] = new CohortDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $cohortIds = array_keys($cohortDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('c.id as cohortId, p.id as programYearId')
            ->from('IliosCoreBundle:Cohort', 'c')
            ->join('c.programYear', 'p')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $cohortIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $cohortDTOs[$arr['cohortId']]->programYear = (int) $arr['programYearId'];
        }

        $related = [
            'courses',
            'learnerGroups',
            'users'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, c.id AS cohortId')->from('IliosCoreBundle:Cohort', 'c')
                ->join("c.{$rel}", 'r')
                ->where($qb->expr()->in('c.id', ':cohortIds'))
                ->orderBy('relId')
                ->setParameter('cohortIds', $cohortIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $cohortDTOs[$arr['cohortId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($cohortDTOs);
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
            $qb->join('c.courses', 'c_courses');
            $qb->andWhere($qb->expr()->in('c_courses.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('learnerGroups', $criteria)) {
            $ids = is_array($criteria['learnerGroups']) ? $criteria['learnerGroups'] : [$criteria['learnerGroups']];
            $qb->join('c.learnerGroups', 'c_learnerGroup');
            $qb->andWhere($qb->expr()->in('c_learnerGroup.id', ':learnerGroups'));
            $qb->setParameter(':learnerGroups', $ids);
        }

        if (array_key_exists('users', $criteria)) {
            $ids = is_array($criteria['users']) ? $criteria['users'] : [$criteria['users']];
            $qb->join('c.users', 'c_users');
            $qb->andWhere($qb->expr()->in('c_users.id', ':users'));
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('c.programYear', 'c_school_programYear');
            $qb->join('c_school_programYear.program', 'c_school_program');
            $qb->join('c_school_program.school', 'c_school');
            $qb->andWhere($qb->expr()->in('c_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('startYears', $criteria)) {
            $ids = is_array($criteria['startYears']) ? $criteria['startYears'] : [$criteria['startYears']];
            $qb->join('c.programYear', 'c_startYears_programYear');
            $qb->andWhere($qb->expr()->in('c_startYears_programYear.startYear', ':startYears'));
            $qb->setParameter(':startYears', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['learnerGroups']);
        unset($criteria['users']);
        unset($criteria['schools']);
        unset($criteria['startYears']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("c.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.'.$sort, $order);
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
