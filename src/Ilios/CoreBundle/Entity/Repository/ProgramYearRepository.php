<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ilios\CoreBundle\Entity\DTO\ProgramYearDTO;

/**
 * Class ProgramYearRepository
 */
class ProgramYearRepository extends EntityRepository implements DTORepositoryInterface
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
        $qb->select('DISTINCT p')->from('IliosCoreBundle:ProgramYear', 'p');

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
        $qb = $this->_em->createQueryBuilder()->select('p')->distinct()->from('IliosCoreBundle:ProgramYear', 'p');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $programYearDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programYearDTOs[$arr['id']] = new ProgramYearDTO(
                $arr['id'],
                $arr['startYear'],
                $arr['locked'],
                $arr['archived'],
                $arr['publishedAsTbd'],
                $arr['published']
            );
        }
        $programYearIds = array_keys($programYearDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('p.id as programYearId, pr.id as programId, c.id as cohortId')
            ->from('IliosCoreBundle:ProgramYear', 'p')
            ->join('p.program', 'pr')
            ->join('p.cohort', 'c')
            ->where($qb->expr()->in('p.id', ':ids'))
            ->setParameter('ids', $programYearIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $programYearDTOs[$arr['programYearId']]->program = (int) $arr['programId'];
            $programYearDTOs[$arr['programYearId']]->cohort = (int) $arr['cohortId'];
        }

        $related = [
            'directors',
            'competencies',
            'terms',
            'objectives',
            'stewards',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, p.id AS programYearId')->from('IliosCoreBundle:ProgramYear', 'p')
                ->join("p.{$rel}", 'r')
                ->where($qb->expr()->in('p.id', ':programYearIds'))
                ->orderBy('relId')
                ->setParameter('programYearIds', $programYearIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $programYearDTOs[$arr['programYearId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($programYearDTOs);
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
            $qb->join('p.cohort', 'c_cohort');
            $qb->join('c_cohort.courses', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('p.cohort', 'se_cohort');
            $qb->join('se_cohort.courses', 'se_course');
            $qb->join('se_course.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('p.terms', 't_term');
            $qb->andWhere($qb->expr()->in('t_term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('p.program', 'py_program');
            $qb->join('py_program.school', 'py_school');
            $qb->andWhere($qb->expr()->in('py_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('startYears', $criteria)) {
            $startYears = is_array($criteria['startYears']) ? $criteria['startYears'] : [$criteria['startYears']];
            $qb->andWhere($qb->expr()->in('p.startYear', ':startYears'));
            $qb->setParameter(':startYears', $startYears);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['terms']);
        unset($criteria['startYears']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("p.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('p.'.$sort, $order);
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
